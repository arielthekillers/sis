<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTahunAjaranToKelas extends Migration
{
    public function up()
    {
        $this->forge->addColumn('kelas', [
            'tahun_ajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
                'after'      => 'keterangan', // or wherever appropriate
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('kelas', 'tahun_ajaran');
    }
}
