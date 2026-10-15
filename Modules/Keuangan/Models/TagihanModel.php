<?php

namespace Modules\Keuangan\Models;

use CodeIgniter\Model;

class TagihanModel extends Model
{
    protected $table            = 'keu_tagihan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'siswa_id', 
        'jenis_pembayaran_id', 
        'judul_tagihan', 
        'nominal_tagihan', 
        'nominal_dibayar', 
        'status', 
        'jatuh_tempo', 
        'tahun_ajaran'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getTagihanSiswa($siswaId)
    {
        return $this->select('keu_tagihan.*, keu_jenis_pembayaran.nama_pembayaran, keu_jenis_pembayaran.tipe_siklus')
            ->join('keu_jenis_pembayaran', 'keu_jenis_pembayaran.id = keu_tagihan.jenis_pembayaran_id')
            ->where('keu_tagihan.siswa_id', $siswaId)
            ->orderBy('keu_tagihan.created_at', 'DESC')
            ->findAll();
    }
}
