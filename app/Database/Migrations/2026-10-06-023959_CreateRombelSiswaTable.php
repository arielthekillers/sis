<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRombelSiswaTable extends Migration
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
            'rombel_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'siswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        
        // Relasi (Opsional, tapi baik untuk integritas data)
        // $this->forge->addForeignKey('rombel_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('rombel_siswa');
    }

    public function down()
    {
        $this->forge->dropTable('rombel_siswa');
    }
}
