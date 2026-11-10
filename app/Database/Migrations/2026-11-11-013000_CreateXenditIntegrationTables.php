<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateXenditIntegrationTables extends Migration
{
    public function up()
    {
        // 1. Buat Tabel Transaksi Xendit
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'invoice_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'external_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'siswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tagihan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['PENDING', 'PAID', 'EXPIRED', 'FAILED'],
                'default'    => 'PENDING',
            ],
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'payment_channel' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'checkout_url' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'paid_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'pembayaran_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'raw_response' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('invoice_id');
        $this->forge->addKey('external_id');
        $this->forge->addKey('siswa_id');
        $this->forge->addKey('tagihan_id');
        $this->forge->createTable('keu_xendit_transaksi', true);

        // 2. Tambahkan default pengaturan Xendit ke tabel pengaturan
        $defaultSettings = [
            [
                'setting_group' => 'xendit',
                'setting_key'   => 'xendit_enabled',
                'setting_value' => '0',
                'setting_label' => 'Aktifkan Gateway Pembayaran Online Xendit',
                'setting_type'  => 'toggle',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_group' => 'xendit',
                'setting_key'   => 'xendit_mode',
                'setting_value' => 'sandbox',
                'setting_label' => 'Mode Lingkungan Xendit (sandbox / production)',
                'setting_type'  => 'select',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_group' => 'xendit',
                'setting_key'   => 'xendit_secret_key',
                'setting_value' => '',
                'setting_label' => 'Secret API Key Xendit',
                'setting_type'  => 'password',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_group' => 'xendit',
                'setting_key'   => 'xendit_public_key',
                'setting_value' => '',
                'setting_label' => 'Public API Key Xendit',
                'setting_type'  => 'text',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_group' => 'xendit',
                'setting_key'   => 'xendit_webhook_token',
                'setting_value' => '',
                'setting_label' => 'Webhook Verification Token',
                'setting_type'  => 'text',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_group' => 'xendit',
                'setting_key'   => 'xendit_invoice_duration',
                'setting_value' => '86400',
                'setting_label' => 'Masa Berlaku Invoice (Detik)',
                'setting_type'  => 'number',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($defaultSettings as $setting) {
            $existing = $this->db->table('pengaturan')->where('setting_key', $setting['setting_key'])->countAllResults();
            if ($existing === 0) {
                $this->db->table('pengaturan')->insert($setting);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('keu_xendit_transaksi', true);
        $this->db->table('pengaturan')->where('setting_group', 'xendit')->delete();
    }
}

