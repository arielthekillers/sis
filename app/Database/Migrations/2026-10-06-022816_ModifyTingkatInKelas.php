<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyTingkatInKelas extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('kelas', [
            'tingkat' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('kelas', [
            'tingkat' => [
                'type'       => 'INT',
                'constraint' => 11,
            ]
        ]);
    }
}
