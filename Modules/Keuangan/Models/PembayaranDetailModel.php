<?php

namespace Modules\Keuangan\Models;

use CodeIgniter\Model;

class PembayaranDetailModel extends Model
{
    protected $table            = 'keu_pembayaran_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pembayaran_id', 
        'tagihan_id', 
        'nominal_alokasi'
    ];

    protected $useTimestamps = false;
}
