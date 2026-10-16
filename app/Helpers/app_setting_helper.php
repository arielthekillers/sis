<?php

if (! function_exists('app_setting')) {
    /**
     * Ambil nilai pengaturan sistem dari database dengan caching static runtime.
     *
     * @param string $key Kunci pengaturan (contoh: 'app_name', 'school_name')
     * @param mixed $default Nilai default jika data tidak ditemukan atau kosong
     * @return mixed
     */
    function app_setting(string $key, mixed $default = null): mixed
    {
        static $settings = null;

        if ($settings === null) {
            $settings = [];
            try {
                $db = \Config\Database::connect();
                if ($db->tableExists('pengaturan')) {
                    $rows = $db->table('pengaturan')->select('setting_key, setting_value')->get()->getResultArray();
                    foreach ($rows as $row) {
                        $settings[$row['setting_key']] = $row['setting_value'];
                    }
                }
            } catch (\Throwable $e) {
                return $default;
            }
        }

        if (array_key_exists($key, $settings)) {
            $val = $settings[$key];
            return ($val !== null && $val !== '') ? $val : $default;
        }

        return $default;
    }
}

if (! function_exists('konfigurasi')) {
    /**
     * Alias fungsi app_setting()
     */
    function konfigurasi(string $key, mixed $default = null): mixed
    {
        return app_setting($key, $default);
    }
}
