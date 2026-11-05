<?php

namespace App\Models;

use App\Entities\Anggota;
use App\Libraries\Doctrine;
use App\Libraries\ValidasiException;

class AnggotaModel
{
    /** @return Anggota[] daftar anggota; keyword (boleh kosong) dicocokkan ke nama. */
    public function cari(string $keyword = ''): array
    {
        return Doctrine::em()
            ->createQuery('SELECT a FROM App\Entities\Anggota a WHERE a.nama LIKE :pola ORDER BY a.nama')
            ->setParameter('pola', '%' . trim($keyword) . '%')
            ->getResult();
    }

    public function cariId(int $id): ?Anggota
    {
        return Doctrine::em()->find(Anggota::class, $id);
    }

    public function simpan(Anggota $anggota): void
    {
        $this->cekNama($anggota->getNama(), $anggota->getId() ?? 0);
        $em = Doctrine::em();
        $em->persist($anggota);
        $em->flush();
    }

    /** Riwayat iuran ikut terhapus lewat ON DELETE CASCADE di database. */
    public function hapus(int $id): void
    {
        $em = Doctrine::em();
        $anggota = $em->find(Anggota::class, $id);
        if ($anggota !== null) {
            $em->remove($anggota);
            $em->flush();
        }
    }

    private function cekNama(string $nama, int $kecualiId): void
    {
        $jumlah = Doctrine::em()
            ->createQuery('SELECT COUNT(a.id) FROM App\Entities\Anggota a WHERE a.nama = :nama AND a.id <> :id')
            ->setParameters(['nama' => $nama, 'id' => $kecualiId])
            ->getSingleScalarResult();

        if ($jumlah > 0) {
            throw new ValidasiException("Anggota bernama {$nama} sudah ada.");
        }
    }
}
