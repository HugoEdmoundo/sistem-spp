<?php

if (!function_exists('terbilang_sederhana')) {
    function terbilang_sederhana($angka) {
        $angka = abs($angka);
        $bilangan = array('', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas');
        
        if ($angka < 12) {
            return $bilangan[$angka];
        } else if ($angka < 20) {
            return $bilangan[$angka - 10] . ' Belas';
        } else if ($angka < 100) {
            $hasil_bagi = floor($angka / 10);
            $hasil_mod = $angka % 10;
            return trim(sprintf('%s Puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
        } else if ($angka < 200) {
            return sprintf('Seratus %s', terbilang_sederhana($angka - 100));
        } else if ($angka < 1000) {
            $hasil_bagi = floor($angka / 100);
            $hasil_mod = $angka % 100;
            return trim(sprintf('%s Ratus %s', $bilangan[$hasil_bagi], terbilang_sederhana($hasil_mod)));
        } else if ($angka < 2000) {
            return trim(sprintf('Seribu %s', terbilang_sederhana($angka - 1000)));
        } else if ($angka < 1000000) {
            $hasil_bagi = floor($angka / 1000);
            $hasil_mod = $angka % 1000;
            return sprintf('%s Ribu %s', terbilang_sederhana($hasil_bagi), terbilang_sederhana($hasil_mod));
        } else if ($angka < 1000000000) {
            $hasil_bagi = floor($angka / 1000000);
            $hasil_mod = $angka % 1000000;
            return trim(sprintf('%s Juta %s', terbilang_sederhana($hasil_bagi), terbilang_sederhana($hasil_mod)));
        }
        
        return 'Angka terlalu besar';
    }
}

if (!function_exists('getNamaBulan')) {
    function getNamaBulan($bulan) {
        $bulanArr = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulanArr[$bulan] ?? 'Bulan ' . $bulan;
    }
}