<?php

namespace Modules\Keuangan\Models;

use CodeIgniter\Model;

class XenditTransaksiModel extends Model
{
    protected $table            = 'keu_xendit_transaksi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'invoice_id',
        'external_id',
        'siswa_id',
        'tagihan_id',
        'user_id',
        'amount',
        'status',
        'payment_method',
        'payment_channel',
        'checkout_url',
        'paid_at',
        'pembayaran_id',
        'raw_response',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Cari transaksi berdasarkan external_id
     */
    public function getByExternalId(string $externalId)
    {
        return $this->where('external_id', $externalId)->first();
    }

    /**
     * Cari transaksi berdasarkan invoice_id dari Xendit
     */
    public function getByInvoiceId(string $invoiceId)
    {
        return $this->where('invoice_id', $invoiceId)->first();
    }
}

