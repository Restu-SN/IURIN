<?php

namespace App\Controllers;

use App\Models\KasModel;
use App\Models\AnggotaModel;
use App\Models\PembayaranModel;
use App\Models\TalanganModel;
use DateTimeImmutable;

/** Rekap kas klub: pemasukan, pengeluaran, saldo, dan struk per hari main (hanya lihat). */
class Kas extends BaseController
{
    public function index()
    {
        $bulan = (string) $this->request->getGet('bulan');
        if (preg_match('/^\d{4}-\d{2}$/', $bulan) !== 1) {
            $bulan = '';
        }
        $kas = new KasModel();
        $masuk = $kas->totalPemasukan($bulan);
        $keluar = $kas->totalPengeluaran($bulan);

        return $this->tampil('kas', [
            'bulan'             => $bulan,
            'pemasukan'         => $masuk,
            'pengeluaran'       => $keluar,
            'saldo'             => $masuk - $keluar,
            'saldoTotal'        => $kas->totalPemasukan() - $kas->totalPengeluaran(),
            'transaksi'         => $kas->transaksi($bulan),
            'hariMain'          => (new PembayaranModel())->sesiBulan($bulan),
        ], 'kas');
    }

    /** Struk satu hari main: siapa yang bayar, pengeluaran, sisa, dan total kas. */
    public function struk(string $tgl = '')
    {
        $tanggal = tanggal_dari($tgl);
        $hadir = $tanggal ? (new PembayaranModel())->daftarTanggal($tanggal) : [];
        $kas = new KasModel();
        $pengeluaran = $tanggal ? $kas->daftarPengeluaranTanggal($tanggal) : [];
        $kasHari = $tanggal ? $kas->kasHari($tanggal) : null;
        $kasLalu = $kasHari?->getKasLalu() ?? 0;
        $guest = $kasHari?->getGuest() ?? 0;
        if ($hadir === [] && $pengeluaran === [] && $kasHari === null) {
            $this->flash('Belum ada catatan main di tanggal itu.', 'warning');

            return redirect()->to('kas');
        }
        $masuk = array_sum(array_map(static fn ($p) => $p->getJumlah(), $hadir)) + ($kasHari?->getMasukGuest() ?? 0);
        $keluar = array_sum(array_map(static fn ($p) => $p->getJumlah(), $pengeluaran));
        [$dariKas, $keluarHari] = KasModel::pisahKasLalu($pengeluaran, $kasLalu);

        // Sisa talangan per anggota sampai tanggal struk ini
        $nama = [];
        foreach ((new AnggotaModel())->cari() as $a) {
            $nama[$a->getId()] = $a->getNama();
        }
        $talangan = [];
        foreach ((new TalanganModel())->rincian($tanggal) as $id => $r) {
            if ($r['total'] > 0 && isset($nama[$id])) {
                $talangan[$nama[$id]] = $r['sisa'];
            }
        }

        return $this->tampil('kas_struk', [
            'talangan'    => $talangan,
            'tanggal'     => $tanggal,
            'nomor'       => 'IUR-' . $tanggal->format('Ymd'),
            'dicetak'     => (new DateTimeImmutable('now', new \DateTimeZone('Asia/Jakarta')))->format('H:i'),
            'hadir'       => $hadir,
            'pengeluaran' => $pengeluaran,
            'masuk'       => $masuk,
            'keluar'      => $keluar,
            'kasLalu'     => $kasLalu,
            'dariKas'     => $dariKas,
            'keluarKas'   => array_sum(array_map(static fn ($p) => $p->getJumlah(), $dariKas)),
            'keluarHari'  => array_sum(array_map(static fn ($p) => $p->getJumlah(), $keluarHari)),
            'pengeluaranHari' => $keluarHari,
            'guest'        => $guest,
            'tarifGuest'   => $kasHari?->getTarifGuest() ?? 0,
        ], 'kas');
    }

    /** Hapus struk = hapus semua catatan tanggal itu (yang main, guest, pengeluaran, kas minggu lalu). */
    public function hapusStruk(string $tgl = '')
    {
        $tanggal = tanggal_dari($tgl);
        if ($tanggal === null) {
            return redirect()->to('kas');
        }
        (new KasModel())->hapusHari($tanggal);
        $this->flash('Struk ' . nama_tanggal($tanggal) . ' beserta catatannya sudah dihapus.');

        return redirect()->to('kas?bulan=' . $tanggal->format('Y-m'));
    }
}
