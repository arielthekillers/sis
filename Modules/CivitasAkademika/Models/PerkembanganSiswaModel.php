<?php

namespace Modules\CivitasAkademika\Models;

use CodeIgniter\Model;

class PerkembanganSiswaModel extends Model
{
    protected $table            = 'perkembangan_siswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'siswa_id', 'kelas_id', 'guru_id', 'tanggal', 
        'kategori', 'judul', 'catatan', 'foto', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'siswa_id' => 'required|numeric',
        'tanggal'  => 'required|valid_date',
        'kategori' => 'required',
        'judul'    => 'required|min_length[3]',
        'catatan'  => 'required',
    ];

    /**
     * Mengambil catatan perkembangan untuk seorang siswa
     */
    public function getBySiswa(int $siswaId, bool $onlyPublished = true)
    {
        $builder = $this->select('perkembangan_siswa.*, guru_staf.nama_lengkap as nama_guru, guru_staf.sapaan as sapaan_guru, kelas.nama_kelas')
            ->join('guru_staf', 'guru_staf.id = perkembangan_siswa.guru_id', 'left')
            ->join('kelas', 'kelas.id = perkembangan_siswa.kelas_id', 'left')
            ->where('perkembangan_siswa.siswa_id', $siswaId)
            ->where('perkembangan_siswa.deleted_at IS NULL');

        if ($onlyPublished) {
            $builder->where('perkembangan_siswa.status', 'published');
        }

        return $builder->orderBy('perkembangan_siswa.tanggal', 'DESC')
                       ->orderBy('perkembangan_siswa.id', 'DESC')
                       ->findAll();
    }
}

