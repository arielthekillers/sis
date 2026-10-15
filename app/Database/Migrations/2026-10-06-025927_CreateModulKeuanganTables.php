<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateModulKeuanganTables extends Migration
{
    public function up()
    {
        // 1. keu_jenis_pembayaran
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_pembayaran' => ['type' => 'VARCHAR', 'constraint' => 255],
            'nominal'         => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'tipe_siklus'     => ['type' => 'ENUM', 'constraint' => ['Bulanan', 'Sekali Bayar'], 'default' => 'Bulanan'],
            'is_active'       => ['type' => 'BOOLEAN', 'default' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('keu_jenis_pembayaran');

        // 2. keu_setup_siswa
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jenis_pembayaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('keu_setup_siswa');

        // 3. keu_tagihan
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jenis_pembayaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'judul_tagihan'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'nominal_tagihan'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'nominal_dibayar'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'status'              => ['type' => 'ENUM', 'constraint' => ['Belum Bayar', 'Mencicil', 'Lunas'], 'default' => 'Belum Bayar'],
            'jatuh_tempo'         => ['type' => 'DATE', 'null' => true],
            'tahun_ajaran'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('keu_tagihan');

        // 4. keu_pembayaran
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'no_kwitansi'       => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'siswa_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tanggal_bayar'     => ['type' => 'DATETIME'],
            'total_bayar'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'metode_pembayaran' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Tunai'],
            'kasir_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true], // Could be connected to users table
            'keterangan'        => ['type' => 'TEXT', 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('keu_pembayaran');

        // 5. keu_pembayaran_detail
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'pembayaran_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tagihan_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nominal_alokasi' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('keu_pembayaran_detail');
    }

    public function down()
    {
        $this->forge->dropTable('keu_pembayaran_detail');
        $this->forge->dropTable('keu_pembayaran');
        $this->forge->dropTable('keu_tagihan');
        $this->forge->dropTable('keu_setup_siswa');
        $this->forge->dropTable('keu_jenis_pembayaran');
    }
}
