<?php

namespace Modules\Keuangan\Models;

use CodeIgniter\Model;

class PengeluaranModel extends Model
{
    protected $table            = 'keu_pengeluaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_transaksi',
        'kategori_id',
        'tanggal',
        'nominal',
        'penerima',
        'keperluan',
        'metode_pembayaran',
        'bukti_transaksi',
        'user_id',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateKodeTransaksi($tanggal = null): string
    {
        $tgl = $tanggal ? strtotime($tanggal) : time();
        $prefix = 'OUT-' . date('Ym', $tgl) . '-';
        
        $last = $this->like('kode_transaksi', $prefix, 'after')
                     ->orderBy('id', 'DESC')
                     ->first();

        if ($last) {
            $lastNumber = (int) substr($last['kode_transaksi'], strlen($prefix));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }
}
