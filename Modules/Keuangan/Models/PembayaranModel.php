<?php

namespace Modules\Keuangan\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table            = 'keu_pembayaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'no_kwitansi', 
        'siswa_id', 
        'tanggal_bayar', 
        'total_bayar', 
        'metode_pembayaran', 
        'kasir_id'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
