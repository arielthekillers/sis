<?php

namespace Modules\CivitasAkademika\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table            = 'siswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nis', 'nisn', 'nama_lengkap', 'nama_panggilan', 'jenis_kelamin',
        'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat', 
        'nama_ayah', 'nama_ibu', 'no_hp_ortu', 'foto', 'status_siswa', 'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'nama_lengkap'  => 'required',
        'jenis_kelamin' => 'required|in_list[L,P]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
