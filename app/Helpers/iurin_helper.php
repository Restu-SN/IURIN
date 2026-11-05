<?php

use CodeIgniter\I18n\Time;

if (! function_exists('rupiah')) {
    function rupiah(int|float $nilai): string
    {
        return 'Rp ' . number_format($nilai, 0, ',', '.');
    }
}

if (! function_exists('angka')) {
    function angka(int|float $nilai): string
    {
        return number_format($nilai, 0, ',', '.');
    }
}

if (! function_exists('minggu_dari')) {
    /** Kode minggu ISO untuk tanggal tertentu, mis. "2026-W45". */
    function minggu_dari(DateTimeInterface $tanggal): string
    {
        return $tanggal->format('o-\WW');
    }
}

if (! function_exists('minggu_ini')) {
    function minggu_ini(): string
    {
        return minggu_dari(new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta')));
    }
}

if (! function_exists('minggu_valid')) {
    function minggu_valid(?string $minggu): bool
    {
        return $minggu !== null && preg_match('/^\d{4}-W\d{2}$/', $minggu) === 1;
    }
}

if (! function_exists('nama_minggu')) {
    /** "2026-W45" menjadi "Minggu 45 (2 Nov - 8 Nov 2026)". */
    function nama_minggu(string $minggu): string
    {
        $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $awal = (new DateTimeImmutable())->setISODate((int) substr($minggu, 0, 4), (int) substr($minggu, 6));
        $akhir = $awal->modify('+6 days');

        return 'Minggu ' . (int) substr($minggu, 6)
            . ' (' . $awal->format('j') . ' ' . $bulan[(int) $awal->format('n')]
            . ' - ' . $akhir->format('j') . ' ' . $bulan[(int) $akhir->format('n')] . ' ' . $akhir->format('Y') . ')';
    }
}

if (! function_exists('tanggal')) {
    function tanggal(DateTimeInterface $tanggal): string
    {
        return $tanggal->format('Y-m-d');
    }
}

if (! function_exists('inisial')) {
    function inisial(string $nama): string
    {
        return mb_strtoupper(mb_substr($nama, 0, 1));
    }
}
