<?php

namespace App\Models;

use App\Entities\Anggota;
use App\Entities\Talangan;
use App\Libraries\Doctrine;
use App\Libraries\ValidasiException;
use DateTimeImmutable;

/**
 * Talangan anggota: total yang ditalangin dikurangi yang sudah terpotong
 * dari iuran main (kolom pembayaran.potong_talangan) = sisa talangan.
 */
class TalanganModel
{
    /** @return Talangan[] talangan milik satu anggota, terbaru di atas. */
    public function daftar(int $anggotaId): array
    {
        return Doctrine::em()
            ->createQuery('SELECT t FROM App\Entities\Talangan t WHERE t.anggota = :id ORDER BY t.tanggal DESC, t.id DESC')
            ->setParameter('id', $anggotaId)
            ->getResult();
    }

    public function tambah(int $anggotaId, DateTimeImmutable $tanggal, int $jumlah, ?string $keterangan): void
    {
        $em = Doctrine::em();
        $anggota = $em->find(Anggota::class, $anggotaId);
        if ($anggota === null) {
            throw new ValidasiException('Anggota tidak ditemukan.');
        }
        if ($jumlah <= 0) {
            throw new ValidasiException('Nominal talangan harus lebih dari 0.');
        }
        $t = new Talangan();
        $t->setAnggota($anggota);
        $t->setTanggal($tanggal);
        $t->setJumlah($jumlah);
        $t->setKeterangan($keterangan);
        $em->persist($t);
        $em->flush();
    }

    /** Hapus satu catatan talangan; mengembalikan id anggotanya (untuk kembali ke halamannya). */
    public function hapus(int $id): ?int
    {
        $em = Doctrine::em();
        $t = $em->find(Talangan::class, $id);
        if ($t === null) {
            return null;
        }
        $anggotaId = $t->getAnggota()->getId();
        $em->remove($t);
        $em->flush();

        return $anggotaId;
    }

    /**
     * Rincian talangan per anggota sampai tanggal tertentu (null = sampai sekarang).
     *
     * @return array<int, array{total: int, terpotong: int, sisa: int}> id anggota => rincian
     */
    public function rincian(?DateTimeImmutable $sampai = null): array
    {
        $em = Doctrine::em();
        $syaratT = $sampai ? ' WHERE t.tanggal <= :tgl' : '';
        $syaratP = $sampai ? ' AND p.tanggalBayar <= :tgl' : '';
        $qT = $em->createQuery('SELECT IDENTITY(t.anggota) AS id, SUM(t.jumlah) AS total FROM App\Entities\Talangan t' . $syaratT . ' GROUP BY t.anggota');
        $qP = $em->createQuery('SELECT IDENTITY(p.anggota) AS id, SUM(p.potongTalangan) AS potong FROM App\Entities\Pembayaran p WHERE p.potongTalangan > 0' . $syaratP . ' GROUP BY p.anggota');
        if ($sampai) {
            $qT->setParameter('tgl', $sampai->format('Y-m-d'));
            $qP->setParameter('tgl', $sampai->format('Y-m-d'));
        }

        $hasil = [];
        foreach ($qT->getArrayResult() as $r) {
            $hasil[(int) $r['id']] = ['total' => (int) $r['total'], 'terpotong' => 0, 'sisa' => (int) $r['total']];
        }
        foreach ($qP->getArrayResult() as $r) {
            $id = (int) $r['id'];
            $hasil[$id] ??= ['total' => 0, 'terpotong' => 0, 'sisa' => 0];
            $hasil[$id]['terpotong'] = (int) $r['potong'];
            $hasil[$id]['sisa'] = max(0, $hasil[$id]['total'] - (int) $r['potong']);
        }

        return $hasil;
    }

    /** @return array<int, int> id anggota => sisa talangan (hanya yang masih ada sisanya). */
    public function sisa(?DateTimeImmutable $sampai = null): array
    {
        $sisa = [];
        foreach ($this->rincian($sampai) as $id => $r) {
            if ($r['sisa'] > 0) {
                $sisa[$id] = $r['sisa'];
            }
        }

        return $sisa;
    }
}
