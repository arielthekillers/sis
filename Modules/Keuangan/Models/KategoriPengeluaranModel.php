<?php

namespace Modules\Keuangan\Models;

use CodeIgniter\Model;

class KategoriPengeluaranModel extends Model
{
    protected $table            = 'keu_kategori_pengeluaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_kategori',
        'deskripsi',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
