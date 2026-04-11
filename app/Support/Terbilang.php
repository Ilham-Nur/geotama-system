<?php

namespace App\Support;

class Terbilang
{
    public static function make(float|int|string $nilai): string
    {
        $nilai = (int) round((float) $nilai);

        if ($nilai === 0) {
            return 'nol';
        }

        if ($nilai < 0) {
            return 'minus ' . trim(self::penyebut(abs($nilai)));
        }

        return trim(self::penyebut($nilai));
    }

    protected static function penyebut(int $nilai): string
    {
        $huruf = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima',
            'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'
        ];

        if ($nilai < 12) {
            return ' ' . $huruf[$nilai];
        } elseif ($nilai < 20) {
            return self::penyebut($nilai - 10) . ' belas';
        } elseif ($nilai < 100) {
            return self::penyebut((int) floor($nilai / 10)) . ' puluh' . self::penyebut($nilai % 10);
        } elseif ($nilai < 200) {
            return ' seratus' . self::penyebut($nilai - 100);
        } elseif ($nilai < 1000) {
            return self::penyebut((int) floor($nilai / 100)) . ' ratus' . self::penyebut($nilai % 100);
        } elseif ($nilai < 2000) {
            return ' seribu' . self::penyebut($nilai - 1000);
        } elseif ($nilai < 1000000) {
            return self::penyebut((int) floor($nilai / 1000)) . ' ribu' . self::penyebut($nilai % 1000);
        } elseif ($nilai < 1000000000) {
            return self::penyebut((int) floor($nilai / 1000000)) . ' juta' . self::penyebut($nilai % 1000000);
        } elseif ($nilai < 1000000000000) {
            return self::penyebut((int) floor($nilai / 1000000000)) . ' miliar' . self::penyebut($nilai % 1000000000);
        } elseif ($nilai < 1000000000000000) {
            return self::penyebut((int) floor($nilai / 1000000000000)) . ' triliun' . self::penyebut($nilai % 1000000000000);
        }

        return '';
    }
}