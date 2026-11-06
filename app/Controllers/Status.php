<?php

namespace App\Controllers;

use App\Models\AnggotaModel;
use App\Models\PembayaranModel;

/** Riwayat Main: ringkasan tiap hari main dan rekap per anggota dalam sebulan. */
class Status extends BaseController
{
    public function index()
    {
        $bulan = (string) $this->request->getGet('bulan');
        if (! bulan_valid($bulan)) {
            $bulan = hari_ini()->format('Y-m');
        }
        $model = new PembayaranModel();
        $sesi = $model->sesiBulan($bulan);
        $rekap = $model->rekapAnggota($bulan);

        $anggota = [];
        foreach ((new AnggotaModel())->cari() as $a) {
            $anggota[] = ['anggota' => $a] + ($rekap[$a->getId()] ?? ['main' => 0, 'total' => 0, 'terakhir' => null]);
        }
        // Yang paling sering main di atas
        usort($anggota, static fn ($x, $y) => [$y['main'], $x['anggota']->getNama()] <=> [$x['main'], $y['anggota']->getNama()]);

        $hariMain = count(array_filter($sesi, static fn ($s) => $s['hadir'] !== [] || $s['guest'] > 0));
        $masuk = array_sum(array_column($sesi, 'masuk'));
        $keluar = array_sum(array_column($sesi, 'keluar'));

        return $this->tampil('status', [
            'bulan'     => $bulan,
            'namaBulan' => nama_bulan($bulan),
            'sesi'      => $sesi,
            'anggota'   => $anggota,
            'hariMain'  => $hariMain,
            'masuk'     => $masuk,
            'keluar'    => $keluar,
        ], 'status');
    }
}
