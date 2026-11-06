<?php

namespace App\Models;

use App\Entities\Anggota;
use App\Entities\Pembayaran;
use App\Libraries\Doctrine;
use DateTimeImmutable;

/**
 * Pembayaran dicatat per tanggal main: yang datang bayar sesuai iurannya,
 * yang tidak datang tidak bayar.
 */
class PembayaranModel
{
    /** @return Pembayaran[] bulan "YYYY-MM"; kosong = semua waktu. Terbaru di atas. */
    public function daftar(string $bulan = ''): array
    {
        $query = Doctrine::em()->createQuery('SELECT p, a FROM App\Entities\Pembayaran p JOIN p.anggota a'
            . ($bulan !== '' ? ' WHERE p.tanggalBayar BETWEEN :awal AND :akhir' : '')
            . ' ORDER BY p.tanggalBayar DESC, a.nama ASC');
        if ($bulan !== '') {
            [$awal, $akhir] = rentang_bulan($bulan);
            $query->setParameter('awal', $awal)->setParameter('akhir', $akhir);
        }

        return $query->getResult();
    }

    /** @return Pembayaran[] anggota yang main pada tanggal tersebut. */
    public function daftarTanggal(DateTimeImmutable $tanggal): array
    {
        return Doctrine::em()
            ->createQuery('SELECT p, a FROM App\Entities\Pembayaran p JOIN p.anggota a WHERE p.tanggalBayar = :tgl ORDER BY a.nama ASC')
            ->setParameter('tgl', $tanggal->format('Y-m-d'))
            ->getResult();
    }

    /**
     * Menyamakan catatan tanggal itu dengan daftar yang dicentang:
     * yang baru dicentang ditambah (nominal = iuran anggota), yang dilepas dihapus.
     *
     * @param int[] $anggotaIds
     * @return array{0: int, 1: int} [jumlah ditambah, jumlah dihapus]
     */
    public function catatHadir(DateTimeImmutable $tanggal, array $anggotaIds): array
    {
        $em = Doctrine::em();
        $sudah = [];
        foreach ($this->daftarTanggal($tanggal) as $p) {
            $sudah[$p->getAnggota()->getId()] = $p;
        }

        $tambah = 0;
        foreach (array_unique(array_map('intval', $anggotaIds)) as $id) {
            if (isset($sudah[$id])) {
                unset($sudah[$id]);

                continue;
            }
            $anggota = $em->find(Anggota::class, $id);
            if ($anggota === null) {
                continue;
            }
            $p = new Pembayaran();
            $p->setAnggota($anggota);
            $p->setTanggalBayar($tanggal);
            $p->setJumlah($anggota->getNominalIuran());
            $em->persist($p);
            $tambah++;
        }
        foreach ($sudah as $p) {
            $em->remove($p);
        }
        $em->flush();

        return [$tambah, count($sudah)];
    }

    /**
     * Rekap per anggota: berapa kali main & total bayar pada bulan itu,
     * serta tanggal terakhir main (kapan pun).
     *
     * @return array<int, array{main: int, total: int, terakhir: ?string}>
     */
    public function rekapAnggota(string $bulan): array
    {
        $em = Doctrine::em();
        [$awal, $akhir] = rentang_bulan($bulan);
        $hasil = [];
        $bulanan = $em->createQuery('SELECT IDENTITY(p.anggota) AS id, COUNT(p.id) AS main, SUM(p.jumlah) AS total
                FROM App\Entities\Pembayaran p WHERE p.tanggalBayar BETWEEN :awal AND :akhir GROUP BY p.anggota')
            ->setParameter('awal', $awal)->setParameter('akhir', $akhir)
            ->getArrayResult();
        foreach ($bulanan as $r) {
            $hasil[(int) $r['id']] = ['main' => (int) $r['main'], 'total' => (int) $r['total'], 'terakhir' => null];
        }
        $terakhir = $em->createQuery('SELECT IDENTITY(p.anggota) AS id, MAX(p.tanggalBayar) AS terakhir
                FROM App\Entities\Pembayaran p GROUP BY p.anggota')
            ->getArrayResult();
        foreach ($terakhir as $r) {
            $hasil[(int) $r['id']] ??= ['main' => 0, 'total' => 0, 'terakhir' => null];
            $hasil[(int) $r['id']]['terakhir'] = (string) $r['terakhir'];
        }

        return $hasil;
    }

    /**
     * Ringkasan tiap hari main dalam sebulan: siapa yang datang, uang masuk,
     * pengeluaran, dan sisanya. Bulan kosong = semua waktu. Terbaru di atas.
     *
     * @return list<array{tanggal: DateTimeImmutable, hadir: string[], guest: int, masuk: int, keluar: int, pengeluaran: list<string>}>
     */
    public function sesiBulan(string $bulan): array
    {
        $sesi = [];
        $baru = static fn (DateTimeImmutable $t) => ['tanggal' => $t, 'hadir' => [], 'guest' => 0, 'masuk' => 0, 'keluar' => 0, 'pengeluaran' => []];
        foreach ($this->daftar($bulan) as $p) {
            $k = $p->getTanggalBayar()->format('Y-m-d');
            $sesi[$k] ??= $baru($p->getTanggalBayar());
            $sesi[$k]['hadir'][] = $p->getAnggota()->getNama();
            $sesi[$k]['masuk'] += $p->getJumlah();
        }
        $kas = new KasModel();
        foreach ($kas->daftarGuest($bulan) as $k) {
            $t = $k->getTanggal()->format('Y-m-d');
            $sesi[$t] ??= $baru($k->getTanggal());
            $sesi[$t]['guest'] = $k->getGuest();
            $sesi[$t]['masuk'] += $k->getMasukGuest();
        }
        foreach (array_reverse($kas->daftarPengeluaran($bulan)) as $p) {
            $k = $p->getTanggal()->format('Y-m-d');
            $sesi[$k] ??= $baru($p->getTanggal());
            $sesi[$k]['keluar'] += $p->getJumlah();
            $sesi[$k]['pengeluaran'][] = $p->getKategori();
        }
        krsort($sesi);

        return array_values($sesi);
    }
}
