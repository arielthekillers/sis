<?php

namespace Modules\Keuangan\Models;

use CodeIgniter\Model;

class SetupSiswaModel extends Model
{
    protected $table            = 'keu_setup_siswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['siswa_id', 'jenis_pembayaran_id'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getSetupBySiswa($siswaId)
    {
        return $this->select('keu_setup_siswa.*, keu_jenis_pembayaran.nama_pembayaran, keu_jenis_pembayaran.nominal, keu_jenis_pembayaran.tipe_siklus')
                    ->join('keu_jenis_pembayaran', 'keu_jenis_pembayaran.id = keu_setup_siswa.jenis_pembayaran_id')
                    ->where('siswa_id', $siswaId)
                    ->orderBy('keu_jenis_pembayaran.nama_pembayaran', 'ASC')
                    ->findAll();
    }
}
