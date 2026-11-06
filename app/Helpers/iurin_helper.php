<?php

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

if (! function_exists('hari_ini')) {
    function hari_ini(): DateTimeImmutable
    {
        return new DateTimeImmutable('today', new DateTimeZone('Asia/Jakarta'));
    }
}

if (! function_exists('tanggal_dari')) {
    /** "2026-11-07" menjadi tanggal; null kalau formatnya salah. */
    function tanggal_dari(?string $teks): ?DateTimeImmutable
    {
        $tgl = DateTimeImmutable::createFromFormat('!Y-m-d', (string) $teks);

        return $tgl !== false && $tgl->format('Y-m-d') === $teks ? $tgl : null;
    }
}

if (! function_exists('bulan_valid')) {
    function bulan_valid(?string $bulan): bool
    {
        return $bulan !== null && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $bulan) === 1;
    }
}

if (! function_exists('rentang_bulan')) {
    /** "2026-11" menjadi ['2026-11-01', '2026-11-30']. */
    function rentang_bulan(string $bulan): array
    {
        $awal = new DateTimeImmutable($bulan . '-01');

        return [$awal->format('Y-m-d'), $awal->modify('last day of this month')->format('Y-m-d')];
    }
}

if (! function_exists('nama_bulan')) {
    /** "2026-11" menjadi "November 2026". */
    function nama_bulan(string $bulan): string
    {
        $nama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return $nama[(int) substr($bulan, 5, 2)] . ' ' . substr($bulan, 0, 4);
    }
}

if (! function_exists('nama_tanggal')) {
    /** Tanggal lengkap dengan hari, mis. "Sabtu, 7 Nov 2026". */
    function nama_tanggal(DateTimeInterface $tanggal): string
    {
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        return $hari[(int) $tanggal->format('w')] . ', ' . $tanggal->format('j') . ' '
            . $bulan[(int) $tanggal->format('n')] . ' ' . $tanggal->format('Y');
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
