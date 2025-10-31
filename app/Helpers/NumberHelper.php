<?php

namespace App\Helpers;

class NumberHelper
{
    public static function terbilang($angka)
    {
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
            return sprintf('Seratus %s', self::terbilang($angka - 100));
        } else if ($angka < 1000) {
            $hasil_bagi = floor($angka / 100);
            $hasil_mod = $angka % 100;
            return trim(sprintf('%s Ratus %s', $bilangan[$hasil_bagi], self::terbilang($hasil_mod)));
        } else if ($angka < 2000) {
            return trim(sprintf('Seribu %s', self::terbilang($angka - 1000)));
        } else if ($angka < 1000000) {
            $hasil_bagi = floor($angka / 1000);
            $hasil_mod = $angka % 1000;
            return sprintf('%s Ribu %s', self::terbilang($hasil_bagi), self::terbilang($hasil_mod));
        } else if ($angka < 1000000000) {
            $hasil_bagi = floor($angka / 1000000);
            $hasil_mod = $angka % 1000000;
            return trim(sprintf('%s Juta %s', self::terbilang($hasil_bagi), self::terbilang($hasil_mod)));
        }
        
        return 'Angka terlalu besar';
    }
}