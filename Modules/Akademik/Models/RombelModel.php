<?php

namespace Modules\Akademik\Models;

use CodeIgniter\Model;

class RombelModel extends Model
{
    protected $table            = 'kelas'; // still using 'kelas' table
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['tingkat', 'nama_kelas', 'wali_kelas', 'keterangan', 'tahun_ajaran'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'tingkat'    => 'required',
        'nama_kelas' => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
