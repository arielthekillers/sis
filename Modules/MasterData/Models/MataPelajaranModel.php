<?php

namespace Modules\MasterData\Models;

use CodeIgniter\Model;

class MataPelajaranModel extends Model
{
    protected $table            = 'mata_pelajaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['kode_mapel', 'nama_mapel', 'kelompok'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'kode_mapel' => 'required|is_unique[mata_pelajaran.kode_mapel,id,{id}]',
        'nama_mapel' => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
