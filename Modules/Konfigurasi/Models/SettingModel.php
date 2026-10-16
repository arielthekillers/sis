<?php

namespace Modules\Konfigurasi\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'pengaturan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'setting_group',
        'setting_key',
        'setting_value',
        'setting_label',
        'setting_type',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil semua pengaturan terkelompok berdasarkan setting_group
     *
     * @return array
     */
    public function getGrouped(): array
    {
        $all = $this->orderBy('id', 'ASC')->findAll();
        $grouped = [];

        foreach ($all as $item) {
            $grouped[$item['setting_group']][$item['setting_key']] = $item;
        }

        return $grouped;
    }

    /**
     * Ambil seluruh setting sebagai key-value pair sederhana
     *
     * @return array
     */
    public function getKeyValuePairs(): array
    {
        $all = $this->findAll();
        $pairs = [];

        foreach ($all as $item) {
            $pairs[$item['setting_key']] = $item['setting_value'];
        }

        return $pairs;
    }

    /**
     * Update nilai pengaturan berdasarkan key
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function setByKey(string $key, mixed $value): bool
    {
        $exist = $this->where('setting_key', $key)->first();
        if ($exist) {
            return $this->update($exist['id'], ['setting_value' => $value]);
        }

        return (bool) $this->insert([
            'setting_key'   => $key,
            'setting_value' => $value,
            'setting_group' => 'umum',
        ]);
    }
}
