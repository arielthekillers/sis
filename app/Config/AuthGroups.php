<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'user';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys
     * are the group names and the values are arrays of the group info.
     *
     * Whatever value you assign as the key will be used to refer to the group
     * when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     *
     * @see https://codeigniter4.github.io/shield/quick_start_guide/using_authorization/#change-available-groups for more info
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Akses penuh ke seluruh sistem SIS.',
        ],
        'direksi' => [
            'title'       => 'Direksi / Manajemen',
            'description' => 'Hak akses pantau strategis dan pelaporan menyeluruh.',
        ],
        'finance_manager' => [
            'title'       => 'Finance Manager',
            'description' => 'Hak akses penuh ke modul Keuangan.',
        ],
        'finance_staff' => [
            'title'       => 'Staff Keuangan',
            'description' => 'Input transaksi dan operasional keuangan harian.',
        ],
        'hr_manager' => [
            'title'       => 'HR Manager',
            'description' => 'Hak akses penuh ke data SDM, Payroll, dan Karyawan.',
        ],
        'hr_staff' => [
            'title'       => 'Staff HR',
            'description' => 'Input kehadiran, data crew, dan operasional SDM.',
        ],
        'user' => [
            'title'       => 'Karyawan Biasa',
            'description' => 'Pengguna standar (Karyawan biasa).',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system.
     *
     * If a permission is not listed here it cannot be used.
     */
    public array $permissions = [
        'users.view'   => 'Dapat melihat daftar pengguna',
        'users.create' => 'Dapat menambah pengguna baru',
        'users.edit'   => 'Dapat mengubah data pengguna dan jabatannya',
        'users.delete' => 'Dapat menghapus pengguna',
        
        'finance.view'   => 'Dapat melihat laporan & transaksi keuangan',
        'finance.create' => 'Dapat membuat transaksi keuangan',
        'finance.edit'   => 'Dapat menyetujui / mengubah data keuangan',
        
        'hr.view'   => 'Dapat melihat daftar absensi dan payroll',
        'hr.create' => 'Dapat input kehadiran dan data HR baru',
        'hr.edit'   => 'Dapat mengubah status karyawan dan menyetujui payroll',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     *
     * This defines group-level permissions.
     */
    public array $matrix = [
        'superadmin' => [
            'users.*',
            'finance.*',
            'hr.*',
        ],
        'direksi' => [
            'users.view',
            'finance.view',
            'hr.view',
        ],
        'finance_manager' => [
            'finance.*',
        ],
        'finance_staff' => [
            'finance.view',
            'finance.create',
        ],
        'hr_manager' => [
            'hr.*',
        ],
        'hr_staff' => [
            'hr.view',
            'hr.create',
        ],
        'user' => [],
    ];
}
