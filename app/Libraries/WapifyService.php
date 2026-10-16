<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;

class WapifyService
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $session;
    protected bool $isActive;

    public function __construct(?string $apiKey = null, ?string $session = null, ?string $apiUrl = null)
    {
        $this->apiKey   = $apiKey ?? (string) app_setting('wapify_api_key', '');
        $this->session  = $session ?? (string) app_setting('wapify_session', 'default');
        $this->apiUrl   = rtrim($apiUrl ?? (string) app_setting('wapify_api_url', 'https://wapify.biz.id/v1'), '/');
        $this->isActive = (bool) app_setting('wapify_is_active', 0);
    }

    /**
     * Cek apakah API Key sudah dikonfigurasi
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Cek apakah modul Wapify aktif dan siap digunakan
     */
    public function isActive(): bool
    {
        return $this->isConfigured() && $this->isActive;
    }

    /**
     * Kirim Pesan Teks WhatsApp
     *
     * @param string $to Nomor tujuan (e.g. 08123456789 atau 628123456789)
     * @param string $message Isi teks pesan
     * @param string|null $session Sesi khusus (opsional, jika ingin override)
     * @return array
     */
    public function sendMessage(string $to, string $message, ?string $session = null): array
    {
        $payload = [
            'session' => $session ?? $this->session,
            'to'      => $this->formatPhone($to),
            'type'    => 'text',
            'text'    => $message,
        ];

        return $this->request('POST', '/messages/send', $payload);
    }

    /**
     * Kirim Gambar WhatsApp dengan Caption
     *
     * @param string $to Nomor tujuan
     * @param string $imageUrl URL publik gambar (JPG, PNG, WebP)
     * @param string $caption Teks caption di bawah gambar
     * @param string|null $session
     * @return array
     */
    public function sendImage(string $to, string $imageUrl, string $caption = '', ?string $session = null): array
    {
        $payload = [
            'session' => $session ?? $this->session,
            'to'      => $this->formatPhone($to),
            'type'    => 'image',
            'url'     => $imageUrl,
            'text'    => $caption,
        ];

        return $this->request('POST', '/messages/send', $payload);
    }

    /**
     * Kirim File Dokumen / PDF WhatsApp
     *
     * @param string $to Nomor tujuan
     * @param string $fileUrl URL publik dokumen
     * @param string $filename Nama file saat didownload penerima (e.g. Kuitansi-123.pdf)
     * @param string $caption Teks pengantar opsional
     * @param string|null $session
     * @return array
     */
    public function sendFile(string $to, string $fileUrl, string $filename, string $caption = '', ?string $session = null): array
    {
        $payload = [
            'session'  => $session ?? $this->session,
            'to'       => $this->formatPhone($to),
            'type'     => 'file',
            'url'      => $fileUrl,
            'filename' => $filename,
            'text'     => $caption,
        ];

        return $this->request('POST', '/messages/send', $payload);
    }

    /**
     * Cek status sesi WhatsApp yang aktif di akun Wapify
     *
     * @param string|null $targetSession Nama sesi yang dicari
     * @return array
     */
    public function checkSessions(?string $targetSession = null): array
    {
        $res = $this->request('GET', '/sessions');
        if ($res['status'] !== 'success') {
            return $res;
        }

        $sessions = $res['data'] ?? [];
        $target = $targetSession ?? $this->session;

        $found = null;
        if (is_array($sessions)) {
            foreach ($sessions as $s) {
                if (isset($s['name']) && $s['name'] === $target) {
                    $found = $s;
                    break;
                }
            }
        }

        return [
            'status'         => 'success',
            'target_session' => $target,
            'is_connected'   => ($found && isset($found['status']) && strtoupper($found['status']) === 'WORKING'),
            'session_detail' => $found,
            'all_sessions'   => $sessions,
        ];
    }

    /**
     * Cek apakah nomor WhatsApp tujuan aktif dan terdaftar
     *
     * @param string $phone
     * @param string|null $session
     * @return array
     */
    public function checkContact(string $phone, ?string $session = null): array
    {
        $ses = $session ?? $this->session;
        $cleanPhone = $this->formatPhone($phone);
        $endpoint = '/contacts/check-exists?session=' . urlencode($ses) . '&phone=' . urlencode($cleanPhone);

        return $this->request('GET', $endpoint);
    }

    /**
     * Eksekusi HTTP Request ke Wapify API
     */
    protected function request(string $method, string $endpoint, ?array $body = null): array
    {
        if (!$this->isConfigured()) {
            return [
                'status'  => 'error',
                'code'    => 400,
                'message' => 'API Key Wapify belum dikonfigurasi di Pengaturan Sistem.',
                'data'    => null,
            ];
        }

        $url = $this->apiUrl . '/' . ltrim($endpoint, '/');

        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
        ];

        if ($method === 'POST') {
            $headers['Content-Type'] = 'application/json';
        }

        try {
            $client = \Config\Services::curlrequest([
                'timeout'         => 15,
                'connect_timeout' => 5,
                'http_errors'     => false,
            ]);

            $options = ['headers' => $headers];
            if ($method === 'POST' && $body !== null) {
                $options['json'] = $body;
            }

            $response = $client->request($method, $url, $options);
            $statusCode = $response->getStatusCode();
            $rawBody = $response->getBody();
            $decoded = json_decode($rawBody, true) ?? [];

            if ($statusCode >= 200 && $statusCode < 300) {
                return [
                    'status'  => 'success',
                    'code'    => $statusCode,
                    'message' => $decoded['message'] ?? 'Berhasil diproses oleh Wapify.',
                    'data'    => $decoded['data'] ?? $decoded,
                ];
            }

            return [
                'status'  => 'error',
                'code'    => $statusCode,
                'message' => $decoded['message'] ?? $decoded['error'] ?? ('Gagal: HTTP ' . $statusCode),
                'data'    => $decoded,
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => 'Gagal menghubungi server Wapify: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    /**
     * Sanitasi format nomor WhatsApp agar bersih (mendukung 08xx -> 628xx atau tetap)
     */
    protected function formatPhone(string $phone): string
    {
        // Hilangkan karakter selain digit
        $clean = preg_replace('/[^0-9]/', '', $phone);
        
        // Ubah 08xx jadi 628xx jika diawali angka 0
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        }

        return $clean;
    }
}
