<?php

namespace Modules\Konfigurasi\Controllers;

use App\Controllers\BaseController;
use Modules\Konfigurasi\Models\SettingModel;
use App\Libraries\ImageUploader;

class Konfigurasi extends BaseController
{
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }

    /**
     * Menampilkan halaman formulir konfigurasi sistem
     */
    public function index()
    {
        $data = [
            'title'    => 'Konfigurasi Sistem',
            'grouped'  => $this->settingModel->getGrouped(),
            'settings' => $this->settingModel->getKeyValuePairs(),
        ];

        return view('Modules\Konfigurasi\Views\index', $data);
    }

    /**
     * Menyimpan pembaruan konfigurasi sistem
     */
    public function save()
    {
        $postData = $this->request->getPost();

        // 1. Tangani Upload Logo Utama jika ada
        $logoFile = $this->request->getFile('school_logo');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $uploader = new ImageUploader('uploads/');
            $result = $uploader->process($logoFile, false, ['thumb' => 150]);
            if ($result['status'] === 'success') {
                $postData['school_logo'] = 'uploads/' . $result['files']['original'];
            }
        }

        // 2. Tangani Upload Favicon jika ada
        $faviconFile = $this->request->getFile('school_favicon');
        if ($faviconFile && $faviconFile->isValid() && !$faviconFile->hasMoved()) {
            $uploader = new ImageUploader('uploads/');
            $result = $uploader->process($faviconFile, true, ['thumb' => 64]);
            if ($result['status'] === 'success') {
                $postData['school_favicon'] = 'uploads/' . $result['files']['original'];
            }
        }

        // 3. Simpan seluruh setting key-value
        $ignoreKeys = ['csrf_test_name'];
        foreach ($postData as $key => $val) {
            if (in_array($key, $ignoreKeys, true)) {
                continue;
            }
            $this->settingModel->setByKey($key, is_string($val) ? trim($val) : $val);
        }

        return redirect()->to(base_url('konfigurasi'))->with('success', 'Konfigurasi sistem berhasil diperbarui!');
    }

    /**
     * AJAX Test Koneksi & Sesi Wapify
     */
    public function testWapifyConnection()
    {
        $apiKey  = $this->request->getPost('api_key') ?: null;
        $session = $this->request->getPost('session') ?: null;
        $apiUrl  = $this->request->getPost('api_url') ?: null;

        $wapify = new \App\Libraries\WapifyService($apiKey, $session, $apiUrl);
        $result = $wapify->checkSessions();

        return $this->response->setJSON($result);
    }

    /**
     * AJAX Test Kirim Pesan WhatsApp
     */
    public function testWapifySend()
    {
        $to = $this->request->getPost('phone');
        $message = $this->request->getPost('message') ?: ('Halo! Ini adalah pesan uji coba integrasi WhatsApp Wapify dari ' . app_setting('app_name', 'SIS'));

        if (empty($to)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Nomor WhatsApp tujuan wajib diisi.',
            ]);
        }

        $apiKey  = $this->request->getPost('api_key') ?: null;
        $session = $this->request->getPost('session') ?: null;
        $apiUrl  = $this->request->getPost('api_url') ?: null;

        $wapify = new \App\Libraries\WapifyService($apiKey, $session, $apiUrl);
        $result = $wapify->sendMessage($to, $message);

        return $this->response->setJSON($result);
    }
}
