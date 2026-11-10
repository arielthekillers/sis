<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrangTuaSiswaTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'siswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'hubungan' => [
                'type'       => 'ENUM',
                'constraint' => ['ayah', 'ibu', 'wali'],
                'default'    => 'wali',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('siswa_id');
        $this->forge->createTable('orang_tua_siswa', true);
    }

    public function down()
    {
        $this->forge->dropTable('orang_tua_siswa', true);
    }
}

