<?php

namespace App\Models;

use App\Entities\Anggota;
use App\Entities\Pembayaran;
use App\Libraries\Doctrine;
use App\Libraries\ValidasiException;

class PembayaranModel
{
    /** @return Pembayaran[] riwayat pembayaran; minggu kosong = semua minggu. */
    public function daftar(string $minggu = ''): array
    {
        $dql = 'SELECT p, a FROM App\Entities\Pembayaran p JOIN p.anggota a'
            . ($minggu !== '' ? ' WHERE p.minggu = :minggu' : '')
            . ' ORDER BY p.tanggalBayar DESC, p.id DESC';
        $query = Doctrine::em()->createQuery($dql);
        if ($minggu !== '') {
            $query->setParameter('minggu', $minggu);
        }

        return $query->getResult();
    }

    public function tambah(Pembayaran $pembayaran, int $anggotaId): void
    {
        $em = Doctrine::em();
        $sudahAda = $em
            ->createQuery('SELECT COUNT(p.id) FROM App\Entities\Pembayaran p WHERE p.anggota = :id AND p.minggu = :minggu')
            ->setParameters(['id' => $anggotaId, 'minggu' => $pembayaran->getMinggu()])
            ->getSingleScalarResult();
        if ($sudahAda > 0) {
            throw new ValidasiException('Anggota ini sudah membayar iuran ' . nama_minggu($pembayaran->getMinggu()) . '.');
        }

        $anggota = $em->find(Anggota::class, $anggotaId);
        if ($anggota === null) {
            throw new ValidasiException('Anggota tidak ditemukan.');
        }
        $pembayaran->setAnggota($anggota);
        $em->persist($pembayaran);
        $em->flush();
    }

    public function hapus(int $id): void
    {
        $em = Doctrine::em();
        $pembayaran = $em->find(Pembayaran::class, $id);
        if ($pembayaran !== null) {
            $em->remove($pembayaran);
            $em->flush();
        }
    }

    /**
     * Status iuran semua anggota pada minggu tersebut.
     * Lunas = ada pembayaran; Menunggak = belum bayar dan minggunya sudah lewat;
     * Belum bayar = belum bayar untuk minggu berjalan / mendatang.
     *
     * @return list<array{anggota: Anggota, status: string, pembayaran: ?Pembayaran}>
     */
    public function statusMinggu(string $minggu): array
    {
        $bayar = [];
        foreach ($this->daftar($minggu) as $p) {
            $bayar[$p->getAnggota()->getId()] = $p;
        }
        // Format YYYY-Www bisa dibandingkan langsung sebagai teks
        $sudahLewat = strcmp($minggu, minggu_ini()) < 0;

        $hasil = [];
        foreach ((new AnggotaModel())->cari() as $anggota) {
            $p = $bayar[$anggota->getId()] ?? null;
            $hasil[] = [
                'anggota'    => $anggota,
                'status'     => $p !== null ? 'LUNAS' : ($sudahLewat ? 'MENUNGGAK' : 'BELUM_BAYAR'),
                'pembayaran' => $p,
            ];
        }

        return $hasil;
    }
}
