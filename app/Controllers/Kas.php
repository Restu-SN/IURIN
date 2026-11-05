<?php

namespace App\Controllers;

use App\Entities\Pengeluaran;
use App\Models\KasModel;
use DateTimeImmutable;

/** Rekap kas klub: pemasukan, pengeluaran, saldo, dan pencatatan pengeluaran. */
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
            'daftarPengeluaran' => $kas->daftarPengeluaran($bulan),
            'hariIni'           => date('Y-m-d'),
        ], 'kas');
    }

    public function simpan()
    {
        $tanggal = DateTimeImmutable::createFromFormat('!Y-m-d', $this->teksPost('tanggal'));
        $kategori = $this->teksPost('kategori');
        $jumlah = $this->angkaPost('jumlah');

        if ($tanggal === false || $kategori === '' || $jumlah <= 0) {
            $this->flash('Lengkapi tanggal, kategori, dan nominal pengeluaran.', 'danger');
        } else {
            $p = new Pengeluaran();
            $p->setTanggal($tanggal);
            $p->setKategori($kategori);
            $p->setKeterangan($this->teksPost('keterangan') ?: null);
            $p->setJumlah($jumlah);
            (new KasModel())->tambahPengeluaran($p);
            $this->flash("Pengeluaran {$kategori} tersimpan.");
        }

        return redirect()->to('kas?bulan=' . $this->teksPost('filterBulan'));
    }

    public function hapus(int $id)
    {
        (new KasModel())->hapusPengeluaran($id);
        $this->flash('Pengeluaran dihapus.');

        return redirect()->to('kas?bulan=' . $this->teksPost('filterBulan'));
    }
}
