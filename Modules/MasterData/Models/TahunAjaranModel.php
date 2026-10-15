<?php

namespace Modules\MasterData\Models;

use CodeIgniter\Model;

class TahunAjaranModel extends Model
{
    protected $table            = 'tahun_ajaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['tahun', 'semester', 'is_active'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'tahun' => 'required',
        'semester' => 'required|in_list[Ganjil,Genap]',
        'is_active' => 'required|in_list[0,1]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
