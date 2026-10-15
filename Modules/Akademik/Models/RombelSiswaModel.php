<?php

namespace Modules\Akademik\Models;

use CodeIgniter\Model;

class RombelSiswaModel extends Model
{
    protected $table            = 'rombel_siswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['rombel_id', 'siswa_id', 'created_at'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    
    /**
     * Mendapatkan daftar siswa dalam sebuah rombel
     */
    public function getSiswaByRombel($rombelId)
    {
        return $this->select('rombel_siswa.id as rs_id, siswa.*')
                    ->join('siswa', 'siswa.id = rombel_siswa.siswa_id')
                    ->where('rombel_siswa.rombel_id', $rombelId)
                    ->orderBy('siswa.nama_lengkap', 'ASC')
                    ->findAll();
    }
}
