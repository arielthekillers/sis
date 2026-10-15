<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = auth()->getProvider();

        $user = new User([
            'username' => 'superadmin',
            'email'    => 'admin@admin.com',
            'password' => 'admin',
        ]);
        $users->save($user);

        // Get the inserted user to add to a group
        $user = $users->findById($users->getInsertID());

        // Add to default group (you can customize groups in Config\AuthGroups.php)
        $user->addGroup('superadmin');
    }
}
