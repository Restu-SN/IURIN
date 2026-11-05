<?php

namespace App\Controllers;

use App\Entities\Pembayaran as PembayaranEntity;
use App\Libraries\ValidasiException;
use App\Models\AnggotaModel;
use App\Models\PembayaranModel;
use DateTimeImmutable;

/** Mencatat pembayaran iuran mingguan dan menampilkan riwayatnya. */
class Pembayaran extends BaseController
{
    public function index(array $tambahan = [])
    {
        $minggu = (string) ($this->request->getGetPost('minggu') ?? '');
        if (! minggu_valid($minggu)) {
            $minggu = minggu_ini();
        }

        return $this->tampil('pembayaran', $tambahan + [
            'minggu'        => $minggu,
            'namaMinggu'    => nama_minggu($minggu),
            'hariIni'       => date('Y-m-d'),
            'daftarAnggota' => (new AnggotaModel())->cari(),
            'riwayat'       => (new PembayaranModel())->daftar($minggu),
            'input'         => [],
        ], 'pembayaran');
    }

    public function simpan()
    {
        $input = [
            'anggotaId'    => $this->angkaPost('anggotaId'),
            'minggu'       => $this->teksPost('minggu'),
            'tanggalBayar' => $this->teksPost('tanggalBayar'),
            'jumlah'       => $this->angkaPost('jumlah'),
            'keterangan'   => $this->teksPost('keterangan'),
        ];

        try {
            $tanggal = DateTimeImmutable::createFromFormat('!Y-m-d', $input['tanggalBayar']);
            if ($tanggal === false) {
                throw new ValidasiException('Tanggal bayar tidak valid.');
            }
            if ($input['anggotaId'] === 0 || ! minggu_valid($input['minggu'])) {
                throw new ValidasiException('Pilih anggota dan minggu iuran.');
            }
            if ($input['jumlah'] <= 0) {
                throw new ValidasiException('Nominal pembayaran harus lebih dari 0.');
            }

            $p = new PembayaranEntity();
            $p->setMinggu($input['minggu']);
            $p->setTanggalBayar($tanggal);
            $p->setJumlah($input['jumlah']);
            $p->setKeterangan($input['keterangan'] !== '' ? $input['keterangan'] : null);
            (new PembayaranModel())->tambah($p, $input['anggotaId']);

            $this->flash('Pembayaran iuran ' . nama_minggu($input['minggu']) . ' tersimpan.');

            return redirect()->to('pembayaran?minggu=' . $input['minggu']);
        } catch (ValidasiException $e) {
            return $this->index(['error' => $e->getMessage(), 'input' => $input]);
        }
    }

    public function hapus(int $id)
    {
        (new PembayaranModel())->hapus($id);
        $this->flash('Pembayaran dihapus.');

        return redirect()->to('pembayaran?minggu=' . $this->teksPost('minggu'));
    }
}
