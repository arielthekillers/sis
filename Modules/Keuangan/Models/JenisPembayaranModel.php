<?php

namespace Modules\Keuangan\Models;

use CodeIgniter\Model;

class JenisPembayaranModel extends Model
{
    protected $table            = 'keu_jenis_pembayaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_pembayaran', 'nominal', 'tipe_siklus', 'is_active'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
