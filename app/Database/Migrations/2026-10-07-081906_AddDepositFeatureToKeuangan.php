<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDepositFeatureToKeuangan extends Migration
{
    public function up()
    {
        // 1. Tambah kolom saldo_deposit di tabel siswa
        $this->forge->addColumn('siswa', [
            'saldo_deposit' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
                'after' => 'status_siswa'
            ]
        ]);

        // 2. Buat tabel history deposit
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'siswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jenis_transaksi' => [
                'type'       => 'ENUM',
                'constraint' => ['Masuk', 'Keluar'],
                'default'    => 'Masuk',
            ],
            'nominal' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'kasir_id' => [
                'type'       => 'INT',
                'constraint' => 11,
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
        $this->forge->createTable('keu_deposit_history');
    }

    public function down()
    {
        $this->forge->dropTable('keu_deposit_history');
        $this->forge->dropColumn('siswa', 'saldo_deposit');
    }
}
