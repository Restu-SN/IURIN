<?php

namespace App\Models;

use App\Entities\Pengeluaran;
use App\Libraries\Doctrine;
use DateTimeImmutable;

/** Pengeluaran klub dan rekap kas (pemasukan dari iuran, pengeluaran, saldo). */
class KasModel
{
    public function tambahPengeluaran(Pengeluaran $pengeluaran): void
    {
        $em = Doctrine::em();
        $em->persist($pengeluaran);
        $em->flush();
    }

    public function hapusPengeluaran(int $id): void
    {
        $em = Doctrine::em();
        $pengeluaran = $em->find(Pengeluaran::class, $id);
        if ($pengeluaran !== null) {
            $em->remove($pengeluaran);
            $em->flush();
        }
    }

    /** @return Pengeluaran[] bulan "YYYY-MM"; kosong = semua bulan. */
    public function daftarPengeluaran(string $bulan = ''): array
    {
        $query = Doctrine::em()->createQuery('SELECT p FROM App\Entities\Pengeluaran p'
            . ($bulan !== '' ? ' WHERE p.tanggal BETWEEN :awal AND :akhir' : '')
            . ' ORDER BY p.tanggal DESC, p.id DESC');
        $this->filterBulan($query, $bulan);

        return $query->getResult();
    }

    /** Pemasukan dihitung dari tanggal bayar iuran; bulan kosong = semua waktu. */
    public function totalPemasukan(string $bulan = ''): int
    {
        return $this->total('SELECT COALESCE(SUM(p.jumlah), 0) FROM App\Entities\Pembayaran p'
            . ($bulan !== '' ? ' WHERE p.tanggalBayar BETWEEN :awal AND :akhir' : ''), $bulan);
    }

    public function totalPengeluaran(string $bulan = ''): int
    {
        return $this->total('SELECT COALESCE(SUM(p.jumlah), 0) FROM App\Entities\Pengeluaran p'
            . ($bulan !== '' ? ' WHERE p.tanggal BETWEEN :awal AND :akhir' : ''), $bulan);
    }

    /**
     * Gabungan pemasukan dan pengeluaran, terbaru di atas.
     *
     * @return list<array{tanggal: DateTimeImmutable, pemasukan: bool, keterangan: string, jumlah: int}>
     */
    public function transaksi(string $bulan = ''): array
    {
        $hasil = [];
        foreach ((new PembayaranModel())->daftar() as $p) {
            if ($bulan === '' || $p->getTanggalBayar()->format('Y-m') === $bulan) {
                $hasil[] = [
                    'tanggal'    => $p->getTanggalBayar(),
                    'pemasukan'  => true,
                    'keterangan' => 'Iuran ' . $p->getAnggota()->getNama() . ' (' . $p->getMinggu() . ')',
                    'jumlah'     => $p->getJumlah(),
                ];
            }
        }
        foreach ($this->daftarPengeluaran($bulan) as $p) {
            $hasil[] = [
                'tanggal'    => $p->getTanggal(),
                'pemasukan'  => false,
                'keterangan' => $p->getKategori() . ($p->getKeterangan() ? ' - ' . $p->getKeterangan() : ''),
                'jumlah'     => $p->getJumlah(),
            ];
        }
        usort($hasil, static fn ($a, $b) => $b['tanggal'] <=> $a['tanggal']);

        return $hasil;
    }

    private function total(string $dql, string $bulan): int
    {
        $query = Doctrine::em()->createQuery($dql);
        $this->filterBulan($query, $bulan);

        return (int) $query->getSingleScalarResult();
    }

    private function filterBulan($query, string $bulan): void
    {
        if ($bulan !== '') {
            $awal = new DateTimeImmutable($bulan . '-01');
            $query->setParameter('awal', $awal->format('Y-m-d'))
                ->setParameter('akhir', $awal->modify('last day of this month')->format('Y-m-d'));
        }
    }
}
