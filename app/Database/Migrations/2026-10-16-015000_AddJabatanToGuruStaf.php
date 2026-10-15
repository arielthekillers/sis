<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJabatanToGuruStaf extends Migration
{
    public function up()
    {
        $this->forge->addColumn('guru_staf', [
            'jabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'status_pegawai',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('guru_staf', ['jabatan']);
    }
}
