<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengeluaranKasTables extends Migration
{
    public function up()
    {
        // 1. Tabel Kategori Pengeluaran
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
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
        $this->forge->createTable('keu_kategori_pengeluaran', true);

        // 2. Tabel Pengeluaran Kas
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_transaksi' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'kategori_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'nominal' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'penerima' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'keperluan' => [
                'type' => 'TEXT',
            ],
            'metode_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Tunai',
            ],
            'bukti_transaksi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->createTable('keu_pengeluaran', true);

        // Insert kategori bawaan
        $now = date('Y-m-d H:i:s');
        $defaultCategories = [
            ['nama_kategori' => 'Operasional & ATK', 'deskripsi' => 'Kertas, spidol, tinta printer, dan perlengkapan kantor', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Sarana & Prasarana', 'deskripsi' => 'Perbaikan fasilitas sekolah, AC, bangku, dan gedung', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Listrik, Air & Internet', 'deskripsi' => 'Tagihan utilitas bulanan sekolah', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Kegiatan Siswa & Lomba', 'deskripsi' => 'Transportasi, pendaftaran lomba, dan kegiatan OSIS', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Konsumsi & Rapat', 'deskripsi' => 'Konsumsi rapat guru, tamu sekolah, dan dinas', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Honor & Insentif Kegiatan', 'deskripsi' => 'Honor kegiatan khusus, narasumber, atau kepanitiaan', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Pengeluaran Lainnya', 'deskripsi' => 'Pengeluaran tak terduga lainnya', 'created_at' => $now, 'updated_at' => $now],
        ];
        $this->db->table('keu_kategori_pengeluaran')->insertBatch($defaultCategories);
    }

    public function down()
    {
        $this->forge->dropTable('keu_pengeluaran', true);
        $this->forge->dropTable('keu_kategori_pengeluaran', true);
    }
}
