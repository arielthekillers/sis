<?php

if (! function_exists('tanggal_indo')) {
    /**
     * Format tanggal ke standar Indonesia (e.g., 17 Agustus 1945)
     *
     * @param string $date (Format Y-m-d atau Y-m-d H:i:s)
     * @param bool $print_day Jika true, tambahkan nama hari
     * @return string
     */
    function tanggal_indo(string $date, bool $print_day = false): string
    {
        $hari = [
            1 => 'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu',
            'Minggu'
        ];
        
        $bulan = [
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $waktu = strtotime($date);
        if ($waktu === false) {
            return $date; // Kembalikan string asli jika format salah
        }

        $tgl = date('j', $waktu);
        $bln = (int) date('n', $waktu);
        $thn = date('Y', $waktu);
        
        $hasil = $tgl . ' ' . $bulan[$bln] . ' ' . $thn;

        // Cek jika butuh waktu/jam
        $waktu_str = date('H:i:s', $waktu);
        if ($waktu_str !== '00:00:00' && strlen($date) > 10) {
            $hasil .= ' ' . date('H:i', $waktu);
        }

        if ($print_day) {
            $urutan_hari = (int) date('N', $waktu);
            $hasil = $hari[$urutan_hari] . ', ' . $hasil;
        }

        return $hasil;
    }
}

if (! function_exists('rupiah')) {
    /**
     * Format angka menjadi mata uang Rupiah
     *
     * @param float|int|string $angka
     * @param bool $with_symbol Jika true, menggunakan awalan "Rp "
     * @return string
     */
    function rupiah($angka, bool $with_symbol = true): string
    {
        $angka = (float) $angka;
        $format = number_format($angka, 0, ',', '.'); // Rupiah biasanya tanpa desimal, atau 2 desimal jika butuh
        return $with_symbol ? 'Rp ' . $format : $format;
    }
}
