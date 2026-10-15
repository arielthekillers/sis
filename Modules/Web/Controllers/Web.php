<?php

namespace Modules\Web\Controllers;

use App\Controllers\BaseController;
use Modules\CivitasAkademika\Models\GuruStafModel;
use Modules\CivitasAkademika\Models\SiswaModel;

class Web extends BaseController
{
    public function index()
    {
        $guruModel = new GuruStafModel();
        $siswaModel = new SiswaModel();

        // Ambil data guru kelas yang aktif untuk ditampilkan di website
        $teachers = $guruModel->where('is_active', 1)
                              ->where('jabatan', 'Guru Kelas')
                              ->orderBy('id', 'ASC')
                              ->findAll();

        // Hitung total siswa aktif
        $totalSiswa = $siswaModel->where('status_siswa', 'Aktif')->countAllResults();

        $data = [
            'title'       => 'RSK Preschool & Kindergarten – Rumah Semut Kecil',
            'description' => 'KB Rumah Semut Kecil: sekolah Islamic Montessori untuk Toddler, Playgroup, dan Kindergarten.',
            'teachers'    => $teachers,
            'totalSiswa'  => $totalSiswa,
        ];

        return view('Modules\Web\Views\index', $data);
    }
}
