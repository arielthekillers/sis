<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWapifySettings extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');
        $wapifySettings = [
            [
                'setting_group' => 'notifikasi',
                'setting_key'   => 'wapify_is_active',
                'setting_value' => '0',
                'setting_label' => 'Status Aktif Integrasi Wapify',
                'setting_type'  => 'boolean',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'notifikasi',
                'setting_key'   => 'wapify_api_url',
                'setting_value' => 'https://wapify.biz.id/v1',
                'setting_label' => 'Wapify API Base URL',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'notifikasi',
                'setting_key'   => 'wapify_api_key',
                'setting_value' => '',
                'setting_label' => 'Wapify Secret API Key (wsk_...)',
                'setting_type'  => 'password',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'notifikasi',
                'setting_key'   => 'wapify_session',
                'setting_value' => 'default',
                'setting_label' => 'Nama Sesi WhatsApp di Wapify',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        foreach ($wapifySettings as $s) {
            $exist = $this->db->table('pengaturan')->where('setting_key', $s['setting_key'])->countAllResults();
            if ($exist === 0) {
                $this->db->table('pengaturan')->insert($s);
            }
        }
    }

    public function down()
    {
        $keys = ['wapify_is_active', 'wapify_api_url', 'wapify_api_key', 'wapify_session'];
        $this->db->table('pengaturan')->whereIn('setting_key', $keys)->delete();
    }
}
