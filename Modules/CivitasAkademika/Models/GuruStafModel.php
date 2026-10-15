<?php

namespace Modules\CivitasAkademika\Models;

use CodeIgniter\Model;

class GuruStafModel extends Model
{
    protected $table            = 'guru_staf';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'nip', 'nama_lengkap', 'nama_panggilan', 
        'sapaan', 'jenis_kelamin', 'no_hp', 'status_pegawai', 'jabatan', 'is_active',
        'foto', 'tahun_masuk'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'nama_lengkap'   => 'required',
        'nama_panggilan' => 'required',
        'sapaan'         => 'required',
        'jenis_kelamin'  => 'required|in_list[L,P]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
