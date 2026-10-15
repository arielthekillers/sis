<?php

namespace Modules\UserManagement\Controllers;

use App\Controllers\BaseController;

class Users extends BaseController
{
    public function index()
    {
        // Nantinya kita akan memanggil model dari Shield di sini
        // $users = auth()->getProvider()->findAll();
        
        $data = [
            'title' => 'Manajemen Pengguna - SIS'
        ];
        
        return view('Modules\UserManagement\Views\index', $data);
    }
}
