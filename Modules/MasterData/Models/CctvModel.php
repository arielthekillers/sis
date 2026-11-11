<?php

namespace Modules\MasterData\Models;

use CodeIgniter\Model;

class CctvModel extends Model
{
    protected $table            = 'cctv_kamera';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_kamera',
        'nama_kamera',
        'lokasi',
        'ip_public',
        'port',
        'stream_url',
        'tipe_stream',
        'jadwal_operasional',
        'resolusi',
        'target_program',
        'keterangan',
        'status',
        'urutan',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'kode_kamera' => 'required|min_length[2]|max_length[50]',
        'nama_kamera' => 'required|min_length[3]|max_length[150]',
        'status'      => 'required|in_list[aktif,nonaktif,maintenance]',
    ];

    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}

