<?php

namespace App\Controllers;

use App\Entities\Anggota as AnggotaEntity;
use App\Libraries\ValidasiException;
use App\Models\AnggotaModel;
use App\Models\PembayaranModel;
use App\Models\TalanganModel;

/** Tambah, lihat, cari, edit, dan hapus anggota. */
class Anggota extends BaseController
{
    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $rekap = (new PembayaranModel())->rekapAnggota(hari_ini()->format('Y-m'));

        return $this->tampil('anggota_list', [
            'q'      => $q,
            'daftar' => (new AnggotaModel())->cari($q),
            'rekap'  => $rekap,
            'talangan' => (new TalanganModel())->sisa(),
        ], 'anggota');
    }

    public function form(?int $id = null)
    {
        $anggota = $id !== null ? (new AnggotaModel())->cariId($id) : null;
        if ($id !== null && $anggota === null) {
            $this->flash('Anggota tidak ditemukan.', 'warning');

            return redirect()->to('anggota');
        }

        return $this->tampil('anggota_form', $this->dataTalangan($anggota) + ['anggota' => $anggota], 'anggota');
    }

    /** Catat talangan baru untuk anggota ini (mis. bayarin sewa lapangan dulu). */
    public function tambahTalangan(int $id)
    {
        try {
            $tanggal = tanggal_dari($this->teksPost('tanggal')) ?? hari_ini();
            $ket = $this->teksPost('keterangan');
            (new TalanganModel())->tambah($id, $tanggal, $this->angkaPost('jumlah'), $ket !== '' ? mb_substr($ket, 0, 255) : null);
            $this->flash('Talangan ' . rupiah($this->angkaPost('jumlah')) . ' tersimpan.');
        } catch (ValidasiException $e) {
            $this->flash($e->getMessage(), 'danger');
        }

        return redirect()->to('anggota/edit/' . $id);
    }

    public function hapusTalangan(int $id)
    {
        $anggotaId = (new TalanganModel())->hapus($id);
        $this->flash('Catatan talangan dihapus.');

        return redirect()->to($anggotaId ? 'anggota/edit/' . $anggotaId : 'anggota');
    }

    /** Data talangan untuk form edit anggota. */
    private function dataTalangan(?AnggotaEntity $anggota): array
    {
        if ($anggota === null || $anggota->getId() === null) {
            return ['daftarTalangan' => [], 'rincianTalangan' => null];
        }
        $model = new TalanganModel();

        return [
            'daftarTalangan'  => $model->daftar($anggota->getId()),
            'rincianTalangan' => $model->rincian()[$anggota->getId()] ?? null,
            'hariIni'         => tanggal(hari_ini()),
        ];
    }

    public function simpan()
    {
        $model = new AnggotaModel();
        $id = $this->angkaPost('id');
        $anggota = $id > 0 ? $model->cariId($id) : new AnggotaEntity();
        if ($anggota === null) {
            return redirect()->to('anggota');
        }
        $anggota->setNama($this->teksPost('nama'));
        $anggota->setNominalIuran($this->angkaPost('nominalIuran'));

        try {
            if ($anggota->getNama() === '') {
                throw new ValidasiException('Nama wajib diisi.');
            }
            $model->simpan($anggota);
            $this->flash($id > 0 ? "Data {$anggota->getNama()} berhasil diperbarui." : "Anggota {$anggota->getNama()} berhasil ditambahkan.");

            return redirect()->to('anggota');
        } catch (ValidasiException $e) {
            return $this->tampil('anggota_form', $this->dataTalangan($anggota) + ['anggota' => $anggota, 'error' => $e->getMessage()], 'anggota');
        }
    }

    public function hapus(int $id)
    {
        (new AnggotaModel())->hapus($id);
        $this->flash('Anggota beserta riwayat mainnya telah dihapus.');

        return redirect()->to('anggota');
    }
}
