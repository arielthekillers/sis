<?php

namespace Modules\Keuangan\Controllers;

use App\Controllers\BaseController;
use App\Libraries\XenditService;
use App\Libraries\WapifyService;
use Modules\Keuangan\Models\XenditTransaksiModel;
use Modules\Keuangan\Models\TagihanModel;
use Modules\Keuangan\Models\PembayaranModel;
use Modules\Keuangan\Models\PembayaranDetailModel;
use Modules\CivitasAkademika\Models\SiswaModel;

class XenditWebhook extends BaseController
{
    protected $xenditService;
    protected $xenditTransaksiModel;
    protected $tagihanModel;
    protected $pembayaranModel;
    protected $pembayaranDetailModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->xenditService         = new XenditService();
        $this->xenditTransaksiModel = new XenditTransaksiModel();
        $this->tagihanModel          = new TagihanModel();
        $this->pembayaranModel       = new PembayaranModel();
        $this->pembayaranDetailModel = new PembayaranDetailModel();
        $this->siswaModel            = new SiswaModel();
    }

    /**
     * Endpoint Webhook Callback Xendit Invoice
     * POST /api/xendit/webhook
     */
    public function index()
    {
        $incomingToken = $this->request->getHeaderLine('x-callback-token');

        // Verifikasi token jika webhook_token diatur di konfigurasi
        if (!$this->xenditService->verifyWebhookToken($incomingToken)) {
            log_message('warning', 'Xendit Webhook: Invalid callback token received.');
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => 'error',
                'message' => 'Unauthorized callback token',
            ]);
        }

        $rawInput = $this->request->getBody();
        $payload  = json_decode($rawInput, true);

        if (empty($payload)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Empty payload',
            ]);
        }

        $invoiceId   = $payload['id'] ?? null;
        $externalId  = $payload['external_id'] ?? null;
        $status      = strtoupper($payload['status'] ?? '');
        $paidAmount  = (float) ($payload['paid_amount'] ?? $payload['amount'] ?? 0);
        $paymentMethod = $payload['payment_method'] ?? 'ONLINE';
        $paymentChannel = $payload['payment_channel'] ?? ($payload['bank_code'] ?? null);
        $paidAt      = !empty($payload['paid_at']) ? date('Y-m-d H:i:s', strtotime($payload['paid_at'])) : date('Y-m-d H:i:s');

        // Cari transaksi di database
        $trx = null;
        if ($externalId) {
            $trx = $this->xenditTransaksiModel->getByExternalId($externalId);
        }
        if (!$trx && $invoiceId) {
            $trx = $this->xenditTransaksiModel->getByInvoiceId($invoiceId);
        }

        if (!$trx) {
            log_message('warning', 'Xendit Webhook: Transaction not found for external_id: ' . $externalId);
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Transaction not found for external_id ' . $externalId,
            ]);
        }

        // Idempotency: Jika sudah berstatus PAID, cukup return 200 OK
        if ($trx['status'] === 'PAID') {
            return $this->response->setStatusCode(200)->setJSON([
                'status'  => 'success',
                'message' => 'Transaction already marked as PAID.',
            ]);
        }

        // Proses jika status PAID atau SETTLED
        if ($status === 'PAID' || $status === 'SETTLED') {
            $db = \Config\Database::connect();
            $db->transStart();

            $pembayaranId = null;

            // 1. Ambil data tagihan
            $tagihan = $this->tagihanModel->find($trx['tagihan_id']);
            if ($tagihan) {
                $newDibayar = (float)$tagihan['nominal_dibayar'] + $paidAmount;
                $newStatus  = ($newDibayar >= (float)$tagihan['nominal_tagihan']) ? 'Lunas' : 'Mencicil';

                $this->tagihanModel->update($tagihan['id'], [
                    'nominal_dibayar' => $newDibayar,
                    'status'          => $newStatus,
                ]);

                // 2. Buat Nomor Kuitansi Resmi
                $noKwitansi = 'PAY-XND-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

                $metodeString = 'Xendit (' . $paymentMethod . ($paymentChannel ? ' - ' . $paymentChannel : '') . ')';

                $this->pembayaranModel->insert([
                    'no_kwitansi'       => $noKwitansi,
                    'siswa_id'          => $trx['siswa_id'],
                    'tanggal_bayar'     => $paidAt,
                    'total_bayar'       => $paidAmount,
                    'metode_pembayaran' => $metodeString,
                    'kasir_id'          => null,
                    'keterangan'        => 'Pembayaran Online Xendit (' . ($trx['invoice_id'] ?: $externalId) . ')',
                ]);

                $pembayaranId = $this->pembayaranModel->insertID();

                // 3. Simpan Detail Alokasi Pembayaran
                $this->pembayaranDetailModel->insert([
                    'pembayaran_id'   => $pembayaranId,
                    'tagihan_id'      => $tagihan['id'],
                    'nominal_alokasi' => $paidAmount,
                ]);
            }

            // 4. Update status transaksi Xendit
            $this->xenditTransaksiModel->update($trx['id'], [
                'status'          => 'PAID',
                'payment_method'  => $paymentMethod,
                'payment_channel' => $paymentChannel,
                'paid_at'         => $paidAt,
                'pembayaran_id'   => $pembayaranId,
                'raw_response'    => $rawInput,
            ]);

            $db->transComplete();

            // 5. Kirim Notifikasi WhatsApp Otomatis ke Orang Tua (Jika nomor tersedia)
            try {
                $siswa = $this->siswaModel->find($trx['siswa_id']);
                if ($siswa && !empty($siswa['no_hp_ortu'])) {
                    $wapify = new WapifyService();
                    if ($wapify->isConfigured()) {
                        $namaSiswa    = $siswa['nama_lengkap'];
                        $namaTagihan  = $tagihan['judul_tagihan'] ?? 'Tagihan Sekolah';
                        $nominalRupiah = rupiah($paidAmount);
                        $pesanWa = "Alhamdulillah! 🌟\n\n"
                                 . "Pembayaran online untuk *{$namaTagihan}* ananda *{$namaSiswa}* sebesar *{$nominalRupiah}* telah kami terima dan diverifikasi otomatis.\n\n"
                                 . "Metode: *{$paymentMethod} " . ($paymentChannel ? "({$paymentChannel})" : "") . "*\n"
                                 . "Waktu: " . tanggal_indo($paidAt, true) . "\n"
                                 . "Status: *LUNAS & TERVERIFIKASI*\n\n"
                                 . "Kuitansi digital dapat diakses kapan saja melalui Portal Orang Tua SIS. Terima kasih atas partisipasi dan kerjasamanya! 🙏";

                        $wapify->sendMessage($siswa['no_hp_ortu'], $pesanWa);
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'Xendit Webhook WA Notification Error: ' . $e->getMessage());
            }

            return $this->response->setStatusCode(200)->setJSON([
                'status'  => 'success',
                'message' => 'Invoice payment processed successfully.',
            ]);
        }

        if ($status === 'EXPIRED') {
            $this->xenditTransaksiModel->update($trx['id'], [
                'status'       => 'EXPIRED',
                'raw_response' => $rawInput,
            ]);

            return $this->response->setStatusCode(200)->setJSON([
                'status'  => 'success',
                'message' => 'Invoice marked as EXPIRED.',
            ]);
        }

        // Status lainnya (misal FAILED)
        $this->xenditTransaksiModel->update($trx['id'], [
            'status'       => $status,
            'raw_response' => $rawInput,
        ]);

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'success',
            'message' => 'Webhook received with status: ' . $status,
        ]);
    }
}

