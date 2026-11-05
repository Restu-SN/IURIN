<?php

namespace App\Controllers;

use App\Models\PembayaranModel;

/** Tabel status iuran (lunas / belum bayar / menunggak) per minggu. */
class Status extends BaseController
{
    public function index()
    {
        $minggu = (string) $this->request->getGet('minggu');
        if (! minggu_valid($minggu)) {
            $minggu = minggu_ini();
        }
        $daftar = (new PembayaranModel())->statusMinggu($minggu);
        $lunas = count(array_filter($daftar, static fn ($s) => $s['status'] === 'LUNAS'));

        return $this->tampil('status', [
            'minggu'      => $minggu,
            'namaMinggu'  => nama_minggu($minggu),
            'daftar'      => $daftar,
            'jumlahLunas' => $lunas,
            'jumlahBelum' => count($daftar) - $lunas,
        ], 'status');
    }
}
