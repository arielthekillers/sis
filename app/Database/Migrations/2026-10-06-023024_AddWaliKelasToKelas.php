<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWaliKelasToKelas extends Migration
{
    public function up()
    {
        $this->forge->addColumn('kelas', [
            'wali_kelas' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'nama_kelas',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('kelas', 'wali_kelas');
    }
}
