<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInitialPasswordToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'initial_password' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'status_message',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'initial_password');
    }
}

