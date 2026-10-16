<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengaturanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'setting_group' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'umum',
            ],
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'setting_label' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
            'setting_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'default'    => 'text',
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
        $this->forge->createTable('pengaturan', true);

        // Seed data awal pengaturan agar tidak kosong dan langsung dapat digunakan
        $now = date('Y-m-d H:i:s');
        $defaults = [
            // Identitas & Profil Lembaga
            [
                'setting_group' => 'identitas',
                'setting_key'   => 'app_name',
                'setting_value' => 'RSK APPS',
                'setting_label' => 'Nama Singkat / Brand Aplikasi',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'identitas',
                'setting_key'   => 'app_tagline',
                'setting_value' => 'Preschool & Kindergarten',
                'setting_label' => 'Tagline / Jenjang Singkat',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'identitas',
                'setting_key'   => 'school_name',
                'setting_value' => 'Rumah Semut Kecil',
                'setting_label' => 'Nama Resmi Sekolah / Yayasan',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'identitas',
                'setting_key'   => 'school_npsn',
                'setting_value' => '69912345',
                'setting_label' => 'NPSN (Nomor Pokok Sekolah Nasional)',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'identitas',
                'setting_key'   => 'school_akreditasi',
                'setting_value' => 'A (Unggul)',
                'setting_label' => 'Status Akreditasi',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'identitas',
                'setting_key'   => 'school_address',
                'setting_value' => 'Jl. Cisitu Indah No. 12, Dago',
                'setting_label' => 'Alamat Lengkap',
                'setting_type'  => 'textarea',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'identitas',
                'setting_key'   => 'school_city',
                'setting_value' => 'Kota Bandung',
                'setting_label' => 'Kota / Kabupaten',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'identitas',
                'setting_key'   => 'school_postal_code',
                'setting_value' => '40135',
                'setting_label' => 'Kode Pos',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'identitas',
                'setting_key'   => 'school_phone',
                'setting_value' => '022-2501234',
                'setting_label' => 'Telepon Kantor',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'identitas',
                'setting_key'   => 'school_whatsapp',
                'setting_value' => '081234567890',
                'setting_label' => 'No. WhatsApp Resmi',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'identitas',
                'setting_key'   => 'school_email',
                'setting_value' => 'admin@rsk.sch.id',
                'setting_label' => 'Email Resmi',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'identitas',
                'setting_key'   => 'school_website',
                'setting_value' => 'https://rsk.sch.id',
                'setting_label' => 'Website Sekolah',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'identitas',
                'setting_key'   => 'school_logo',
                'setting_value' => 'uploads/logo.png',
                'setting_label' => 'Logo Utama Sekolah',
                'setting_type'  => 'image',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // Pejabat & Penandatangan
            [
                'setting_group' => 'pejabat',
                'setting_key'   => 'headmaster_name',
                'setting_value' => 'Hj. Siti Nurhaliza, S.Pd., M.M.',
                'setting_label' => 'Nama Kepala Sekolah',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'pejabat',
                'setting_key'   => 'headmaster_nip',
                'setting_value' => '19850101 201001 2 001',
                'setting_label' => 'NIP / NUPTK Kepala Sekolah',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'pejabat',
                'setting_key'   => 'cashier_name',
                'setting_value' => 'Admin Keuangan',
                'setting_label' => 'Nama Kasir / Bendahara (Default Cetak)',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // Keuangan & Kuitansi
            [
                'setting_group' => 'keuangan',
                'setting_key'   => 'receipt_prefix',
                'setting_value' => 'KW',
                'setting_label' => 'Prefix Nomor Kuitansi',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'keuangan',
                'setting_key'   => 'receipt_footer',
                'setting_value' => 'Kuitansi ini adalah bukti pembayaran yang sah dan diterbitkan secara resmi oleh sistem informasi sekolah. Harap disimpan dengan baik.',
                'setting_label' => 'Catatan Kaki Kuitansi (Footer)',
                'setting_type'  => 'textarea',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'keuangan',
                'setting_key'   => 'bank_name',
                'setting_value' => 'Bank Mandiri',
                'setting_label' => 'Nama Bank Sekolah',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'keuangan',
                'setting_key'   => 'bank_account_number',
                'setting_value' => '130-00-1234567-8',
                'setting_label' => 'Nomor Rekening Bank',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'setting_group' => 'keuangan',
                'setting_key'   => 'bank_account_holder',
                'setting_value' => 'Yayasan Rumah Semut Kecil',
                'setting_label' => 'Atas Nama Rekening Bank',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],

            // Pengaturan Sistem Lainnya
            [
                'setting_group' => 'sistem',
                'setting_key'   => 'timezone',
                'setting_value' => 'Asia/Jakarta',
                'setting_label' => 'Zona Waktu Default',
                'setting_type'  => 'text',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        $this->db->table('pengaturan')->insertBatch($defaults);
    }

    public function down()
    {
        $this->forge->dropTable('pengaturan', true);
    }
}
