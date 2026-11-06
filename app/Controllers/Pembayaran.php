<?php

namespace App\Controllers;

use App\Entities\Pengeluaran;
use App\Models\AnggotaModel;
use App\Models\KasModel;
use App\Models\PembayaranModel;

/**
 * Catat Main: centang siapa yang datang pada tanggal main (otomatis bayar
 * sesuai iurannya) dan catat pengeluaran hari itu (air, parkir, kok, ...).
 */
class Pembayaran extends BaseController
{
    public function index()
    {
        $tanggal = tanggal_dari((string) $this->request->getGet('tanggal')) ?? hari_ini();
        $kas = new KasModel();
        $hadir = (new PembayaranModel())->daftarTanggal($tanggal);
        $pengeluaran = $kas->daftarPengeluaranTanggal($tanggal);
        $kasHari = $kas->kasHari($tanggal);
        $guest = $kasHari?->getGuest() ?? 0;
        $tarifGuest = $kasHari !== null && $guest > 0 ? $kasHari->getTarifGuest() : 15000;
        $masuk = array_sum(array_map(static fn ($p) => $p->getJumlah(), $hadir)) + $guest * $tarifGuest;
        $keluar = array_sum(array_map(static fn ($p) => $p->getJumlah(), $pengeluaran));
        $kasLalu = $kasHari?->getKasLalu() ?? 0;
        [$dariKas] = KasModel::pisahKasLalu($pengeluaran, $kasLalu);

        return $this->tampil('pembayaran', [
            'dariKas'       => $dariKas,
            'keluarKas'     => array_sum(array_map(static fn ($p) => $p->getJumlah(), $dariKas)),
            'guest'          => $guest,
            'tarifGuest'     => $tarifGuest,
            'tanggal'       => $tanggal,
            'daftarAnggota' => (new AnggotaModel())->cari(),
            'hadir'         => $hadir,
            'hadirIds'      => array_map(static fn ($p) => $p->getAnggota()->getId(), $hadir),
            'pengeluaran'   => $pengeluaran,
            'masuk'         => $masuk,
            'keluar'        => $keluar,
            'kategori'      => Pengeluaran::KATEGORI,
            'kasLalu'       => $kasLalu,
            'kasSebelum'    => $kas->totalKasSebelum($tanggal),
        ], 'pembayaran');
    }

    /** Simpan daftar yang datang (yang dicentang). */
    public function simpan()
    {
        $tanggal = tanggal_dari($this->teksPost('tanggal'));
        if ($tanggal === null) {
            $this->flash('Tanggal main tidak valid.', 'danger');

            return redirect()->to('pembayaran');
        }
        $ids = $this->request->getPost('hadir');
        [$tambah, $hapus] = (new PembayaranModel())->catatHadir($tanggal, is_array($ids) ? $ids : []);

        // Guest (tanpa nama): cukup jumlah orangnya, uangnya = orang x tarif
        $kas = new KasModel();
        $guestLama = $kas->kasHari($tanggal)?->getGuest() ?? 0;
        $guest = max(0, min(99, $this->angkaPost('guest')));
        $kas->simpanGuest($tanggal, $guest, $this->angkaPost('tarifGuest'));

        $pesan = [];
        if ($guest !== $guestLama) {
            $pesan[] = $guest > 0 ? "guest {$guest} orang" : 'guest dihapus';
        }
        if ($tambah > 0) {
            $pesan[] = "{$tambah} orang ditambahkan";
        }
        if ($hapus > 0) {
            $pesan[] = "{$hapus} orang dihapus";
        }
        $this->flash($pesan === [] ? 'Tidak ada perubahan.' : 'Catatan main ' . nama_tanggal($tanggal) . ': ' . implode(', ', $pesan) . '.');

        return redirect()->to('pembayaran?tanggal=' . tanggal($tanggal));
    }

    /** Kas minggu lalu (uang bawaan) yang ditambahkan ke sisa hari ini. */
    public function kasLalu()
    {
        $tanggal = tanggal_dari($this->teksPost('tanggal'));
        if ($tanggal === null) {
            $this->flash('Tanggal main tidak valid.', 'danger');

            return redirect()->to('pembayaran');
        }
        $jumlah = max(0, $this->angkaPost('kasLalu'));
        $kas = new KasModel();
        $kas->simpanKasLalu($tanggal, $jumlah);
        // Beli air & kok diambil dari kas minggu lalu, jadi diisi di kotak yang sama
        $kas->simpanPengeluaranHari($tanggal, $this->isianPengeluaran(Pengeluaran::DARI_KAS_LALU));
        $this->flash('Kas minggu lalu ' . nama_tanggal($tanggal) . ' tersimpan.');

        return redirect()->to('pembayaran?tanggal=' . tanggal($tanggal));
    }

    /**
     * Pengeluaran hari main: tiap jenis (air, parkir, kok, lainnya) punya
     * kolom nominal sendiri. Kosongkan nominal untuk menghapusnya.
     */
    public function pengeluaran()
    {
        $tanggal = tanggal_dari($this->teksPost('tanggal'));
        if ($tanggal === null) {
            $this->flash('Tanggal main tidak valid.', 'danger');

            return redirect()->to('pembayaran');
        }
        $isian = $this->isianPengeluaran(array_values(array_filter(Pengeluaran::KATEGORI, static fn ($k) => ! Pengeluaran::dariKasLalu($k))));
        (new KasModel())->simpanPengeluaranHari($tanggal, $isian);
        $total = array_sum(array_column($isian, 'jumlah'));
        $this->flash('Pengeluaran ' . nama_tanggal($tanggal) . ' tersimpan: ' . rupiah($total) . '.');

        return redirect()->to('pembayaran?tanggal=' . tanggal($tanggal));
    }

    /**
     * Ambil isian nominal (jumlah[i]) & catatan (keterangan[i]) dari form untuk
     * kategori tertentu saja; i = urutan kategori di Pengeluaran::KATEGORI.
     *
     * @param string[] $kategoriDipakai
     * @return array<string, array{jumlah: int, keterangan: ?string}>
     */
    private function isianPengeluaran(array $kategoriDipakai): array
    {
        $jumlah = (array) ($this->request->getPost('jumlah') ?? []);
        $catatan = (array) ($this->request->getPost('keterangan') ?? []);
        $isian = [];
        foreach (Pengeluaran::KATEGORI as $i => $kategori) {
            if (! in_array($kategori, $kategoriDipakai, true)) {
                continue;
            }
            $ket = trim((string) ($catatan[$i] ?? ''));
            $isian[$kategori] = [
                'jumlah'     => max(0, (int) str_replace('.', '', (string) ($jumlah[$i] ?? ''))),
                'keterangan' => $ket !== '' ? mb_substr($ket, 0, 255) : null,
            ];
        }

        return $isian;
    }
}
