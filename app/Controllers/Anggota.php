<?php

namespace App\Controllers;

use App\Entities\Anggota as AnggotaEntity;
use App\Libraries\ValidasiException;
use App\Models\AnggotaModel;
use App\Models\PembayaranModel;

/** Tambah, lihat, cari, edit, dan hapus anggota. */
class Anggota extends BaseController
{
    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $status = [];
        foreach ((new PembayaranModel())->statusMinggu(minggu_ini()) as $s) {
            $status[$s['anggota']->getId()] = $s['status'];
        }

        return $this->tampil('anggota_list', [
            'q'      => $q,
            'daftar' => (new AnggotaModel())->cari($q),
            'status' => $status,
        ], 'anggota');
    }

    public function form(?int $id = null)
    {
        $anggota = $id !== null ? (new AnggotaModel())->cariId($id) : null;
        if ($id !== null && $anggota === null) {
            $this->flash('Anggota tidak ditemukan.', 'warning');

            return redirect()->to('anggota');
        }

        return $this->tampil('anggota_form', ['anggota' => $anggota], 'anggota');
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
            return $this->tampil('anggota_form', ['anggota' => $anggota, 'error' => $e->getMessage()], 'anggota');
        }
    }

    public function hapus(int $id)
    {
        (new AnggotaModel())->hapus($id);
        $this->flash('Anggota beserta riwayat iurannya telah dihapus.');

        return redirect()->to('anggota');
    }
}
