<?php

namespace Modules\CivitasAkademika\Models;

use CodeIgniter\Model;

class OrangTuaSiswaModel extends Model
{
    protected $table            = 'orang_tua_siswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'siswa_id', 'hubungan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Dapatkan daftar anak untuk user_id (orang tua) tertentu
     */
    public function getAnakByUserId(int $userId)
    {
        return $this->select('orang_tua_siswa.*, siswa.nis, siswa.nisn, siswa.nama_lengkap, siswa.nama_panggilan, siswa.jenis_kelamin, siswa.foto, siswa.status_siswa, siswa.program')
            ->join('siswa', 'siswa.id = orang_tua_siswa.siswa_id')
            ->where('orang_tua_siswa.user_id', $userId)
            ->where('siswa.deleted_at IS NULL')
            ->findAll();
    }
}

