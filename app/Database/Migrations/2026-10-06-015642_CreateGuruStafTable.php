<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGuruStafTable extends Migration
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
                'null'       => true, // Can be null if they don't have login yet
            ],
            'nip' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true, // Not all levels require NIP
            ],
            'nama_lengkap' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'nama_panggilan' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'sapaan' => [
                'type'       => 'VARCHAR',
                'constraint' => '20', // Bapak, Ibu, Miss, Ustadz, Ustadzah, dll
            ],
            'jenis_kelamin' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'P'],
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'status_pegawai' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        // Optional foreign key to users table if using CI4 Shield (assumes table is named 'users')
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('guru_staf');
    }

    public function down()
    {
        $this->forge->dropTable('guru_staf');
    }
}
