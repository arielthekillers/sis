<?php

namespace Modules\Keuangan\Models;

use CodeIgniter\Model;

class DepositHistoryModel extends Model
{
    protected $table            = 'keu_deposit_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'siswa_id',
        'jenis_transaksi',
        'nominal',
        'keterangan',
        'kasir_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
