<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoTahunMasukToGuruStaf extends Migration
{
    public function up()
    {
        $this->forge->addColumn('guru_staf', [
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'status_pegawai',
            ],
            'tahun_masuk' => [
                'type'       => 'INT',
                'constraint' => 4,
                'null'       => true,
                'after'      => 'foto',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('guru_staf', ['foto', 'tahun_masuk']);
    }
}
