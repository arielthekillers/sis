<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProgramToSiswa extends Migration
{
    public function up()
    {
        $fields = [
            'program' => [
                'type'       => 'ENUM',
                'constraint' => ['Reguler', 'Fullday'],
                'default'    => 'Reguler',
                'after'      => 'status_siswa',
            ],
        ];

        $this->forge->addColumn('siswa', $fields);

        // Berikan contoh program Fullday untuk siswa id 63
        $this->db->table('siswa')->where('id', 63)->update(['program' => 'Fullday']);
    }

    public function down()
    {
        $this->forge->dropColumn('siswa', 'program');
    }
}
