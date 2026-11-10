<?php

namespace App\Libraries;

/**
 * Xendit Payment Gateway Service
 * 
 * Menangani integrasi pembuatan tagihan (Invoice), pengecekan saldo/koneksi,
 * serta verifikasi webhook callback pembayaran Xendit.
 */
class XenditService
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $mode;
    protected bool $enabled;
    protected string $webhookToken;
    protected int $invoiceDuration;

    protected string $baseUrl = 'https://api.xendit.co';

    public function __construct(
        ?string $secretKey = null, 
        ?string $mode = null,
        ?string $webhookToken = null
    ) {
        $this->secretKey       = $secretKey ?? (string) app_setting('xendit_secret_key', '');
        $this->publicKey       = (string) app_setting('xendit_public_key', '');
        $this->mode            = $mode ?? (string) app_setting('xendit_mode', 'sandbox');
        $this->enabled         = ((string) app_setting('xendit_enabled', '0')) === '1';
        $this->webhookToken    = $webhookToken ?? (string) app_setting('xendit_webhook_token', '');
        $this->invoiceDuration = (int) app_setting('xendit_invoice_duration', 86400);
    }

    /**
     * Cek apakah gateway Xendit diaktifkan oleh sekolah
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->secretKey);
    }

    /**
     * Dapatkan mode saat ini (sandbox / production)
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Uji koneksi API Xendit menggunakan Secret Key
     */
    public function checkConnection(?string $testKey = null): array
    {
        $keyToTest = $testKey ?: $this->secretKey;

        if (empty($keyToTest)) {
            return [
                'status'  => 'error',
                'message' => 'Secret API Key belum diisi.',
            ];
        }

        // Panggil endpoint /balance untuk menguji autentikasi
        $endpoint = $this->baseUrl . '/balance';
        $response = $this->request('GET', $endpoint, null, $keyToTest);

        if ($response['http_code'] === 200 && isset($response['body']['balance'])) {
            $formattedBalance = number_format($response['body']['balance'], 0, ',', '.');
            return [
                'status'  => 'success',
                'message' => "Koneksi ke Xendit Berhasil! Akun terhubung (Saldo Akun: Rp {$formattedBalance}).",
                'balance' => $response['body']['balance'],
            ];
        }

        // Beberapa akun restricted API key mungkin tidak memiliki izin balance, kita fallback coba /v2/invoices?limit=1
        $fallback = $this->request('GET', $this->baseUrl . '/v2/invoices?limit=1', null, $keyToTest);
        if ($fallback['http_code'] === 200) {
            return [
                'status'  => 'success',
                'message' => 'Koneksi ke Xendit Berhasil! API Key aktif dan siap digunakan.',
            ];
        }

        $errMsg = $response['body']['message'] ?? ($fallback['body']['message'] ?? 'Gagal terhubung ke server Xendit. Periksa Secret Key Anda.');
        return [
            'status'  => 'error',
            'message' => 'Gagal autentikasi Xendit: ' . $errMsg,
        ];
    }

    /**
     * Buat Tagihan / Invoice Xendit
     *
     * @param array $params [
     *   'external_id'          => string,
     *   'amount'               => float,
     *   'description'          => string,
     *   'payer_email'          => ?string,
     *   'customer'             => ?array,
     *   'items'                => ?array,
     *   'success_redirect_url' => ?string,
     *   'failure_redirect_url' => ?string,
     * ]
     * @return array
     */
    public function createInvoice(array $params): array
    {
        if (empty($this->secretKey)) {
            return [
                'status'  => 'error',
                'message' => 'Pembayaran online belum dapat diproses karena Secret Key Xendit belum dikonfigurasi.',
            ];
        }

        $endpoint = $this->baseUrl . '/v2/invoices';

        $payload = [
            'external_id'          => $params['external_id'],
            'amount'               => (float) $params['amount'],
            'description'          => $params['description'] ?? 'Pembayaran Administrasi Sekolah',
            'invoice_duration'     => (int) ($params['invoice_duration'] ?? $this->invoiceDuration),
            'currency'             => 'IDR',
        ];

        if (!empty($params['payer_email'])) {
            $payload['payer_email'] = $params['payer_email'];
        }

        if (!empty($params['customer'])) {
            $payload['customer'] = $params['customer'];
        }

        if (!empty($params['items'])) {
            $payload['items'] = $params['items'];
        }

        if (!empty($params['success_redirect_url'])) {
            $payload['success_redirect_url'] = $params['success_redirect_url'];
        }

        if (!empty($params['failure_redirect_url'])) {
            $payload['failure_redirect_url'] = $params['failure_redirect_url'];
        }

        $response = $this->request('POST', $endpoint, $payload);

        if (in_array($response['http_code'], [200, 201], true) && !empty($response['body']['invoice_url'])) {
            return [
                'status'      => 'success',
                'invoice_id'  => $response['body']['id'],
                'invoice_url' => $response['body']['invoice_url'],
                'expiry_date' => $response['body']['expiry_date'] ?? null,
                'data'        => $response['body'],
            ];
        }

        $errMsg = $response['body']['message'] ?? 'Terjadi kesalahan saat berkomunikasi dengan server Xendit.';
        if (!empty($response['body']['errors'])) {
            $errMsg .= ' (' . json_encode($response['body']['errors']) . ')';
        }

        return [
            'status'    => 'error',
            'message'   => $errMsg,
            'http_code' => $response['http_code'],
        ];
    }

    /**
     * Dapatkan detail Invoice Xendit berdasarkan invoice_id
     */
    public function getInvoice(string $invoiceId): ?array
    {
        $endpoint = $this->baseUrl . '/v2/invoices/' . urlencode($invoiceId);
        $response = $this->request('GET', $endpoint);

        if ($response['http_code'] === 200) {
            return $response['body'];
        }

        return null;
    }

    /**
     * Verifikasi token callback dari webhook Xendit
     */
    public function verifyWebhookToken(?string $incomingToken): bool
    {
        if (empty($this->webhookToken)) {
            // Jika token belum diset di pengaturan, loloskan namun catat log
            return true;
        }

        return hash_equals(trim($this->webhookToken), trim((string)$incomingToken));
    }

    /**
     * Helper cURL request ke API Xendit
     */
    private function request(string $method, string $url, ?array $payload = null, ?string $customKey = null): array
    {
        $key = $customKey ?: $this->secretKey;
        $ch  = curl_init($url);

        $headers = [
            'Authorization: Basic ' . base64_encode($key . ':'),
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($payload !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            }
        } elseif (strtoupper($method) !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        }

        $rawResponse = curl_exec($ch);
        $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError   = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'http_code' => 0,
                'body'      => ['message' => 'cURL Error: ' . $curlError],
            ];
        }

        $decoded = json_decode($rawResponse, true) ?: [];

        return [
            'http_code' => $httpCode,
            'body'      => $decoded,
        ];
    }
}

