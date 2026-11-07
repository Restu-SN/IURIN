<?php

namespace App\Models;

use App\Entities\KasHari;
use App\Entities\Pengeluaran;
use App\Libraries\Doctrine;
use DateTimeImmutable;

/** Pengeluaran klub dan rekap kas (pemasukan dari yang main, pengeluaran, saldo). */
class KasModel
{
    /** Kas minggu lalu yang dicatat pada tanggal main itu (0 kalau belum diisi). */
    public function kasLalu(DateTimeImmutable $tanggal): int
    {
        return $this->kasHari($tanggal)?->getKasLalu() ?? 0;
    }

    /** Simpan kas minggu lalu untuk tanggal itu; 0 = hapus. */
    public function simpanKasLalu(DateTimeImmutable $tanggal, int $jumlah): void
    {
        $this->ubahKasHari($tanggal, static fn (KasHari $k) => $k->setKasLalu(max(0, $jumlah)));
    }

    /** Simpan guest tanpa nama: jumlah orang x tarif. 0 orang = tidak ada guest. */
    public function simpanGuest(DateTimeImmutable $tanggal, int $orang, int $tarif): void
    {
        $this->ubahKasHari($tanggal, static function (KasHari $k) use ($orang, $tarif) {
            $k->setGuest(max(0, $orang));
            $k->setTarifGuest(max(0, $tarif));
        });
    }

    /** @return KasHari[] catatan guest dalam sebulan; kosong = semua waktu. */
    public function daftarGuest(string $bulan = ''): array
    {
        $query = Doctrine::em()->createQuery('SELECT k FROM App\Entities\KasHari k WHERE k.guest > 0'
            . ($bulan !== '' ? ' AND k.tanggal BETWEEN :awal AND :akhir' : '')
            . ' ORDER BY k.tanggal DESC');
        $this->filterBulan($query, $bulan);

        return $query->getResult();
    }

    /**
     * Total kas pada hari main terakhir sebelum tanggal ini
     * (kas minggu lalu + masuk - keluar), untuk patokan saat mengisi.
     *
     * @return array{tanggal: DateTimeImmutable, total: int}|null
     */
    public function totalKasSebelum(DateTimeImmutable $tanggal): ?array
    {
        $em = Doctrine::em();
        $tgl = $tanggal->format('Y-m-d');
        $terakhir = null;
        foreach ([
            'SELECT MAX(p.tanggalBayar) FROM App\Entities\Pembayaran p WHERE p.tanggalBayar < :tgl',
            'SELECT MAX(p.tanggal) FROM App\Entities\Pengeluaran p WHERE p.tanggal < :tgl',
            'SELECT MAX(k.tanggal) FROM App\Entities\KasHari k WHERE k.tanggal < :tgl',
        ] as $dql) {
            $max = $em->createQuery($dql)->setParameter('tgl', $tgl)->getSingleScalarResult();
            if ($max !== null && ($terakhir === null || $max > $terakhir)) {
                $terakhir = (string) $max;
            }
        }
        if ($terakhir === null) {
            return null;
        }
        $hari = new DateTimeImmutable($terakhir);

        return ['tanggal' => $hari, 'total' => $this->totalKasHari($hari)];
    }

    /** Total kas di akhir satu hari main: kas minggu lalu + uang masuk - pengeluaran. */
    public function totalKasHari(DateTimeImmutable $tanggal): int
    {
        $em = Doctrine::em();
        $tgl = $tanggal->format('Y-m-d');
        $masuk = $em->createQuery('SELECT COALESCE(SUM(p.jumlah), 0) FROM App\Entities\Pembayaran p WHERE p.tanggalBayar = :tgl')
            ->setParameter('tgl', $tgl)->getSingleScalarResult();
        $keluar = $em->createQuery('SELECT COALESCE(SUM(p.jumlah), 0) FROM App\Entities\Pengeluaran p WHERE p.tanggal = :tgl')
            ->setParameter('tgl', $tgl)->getSingleScalarResult();

        $k = $this->kasHari($tanggal);

        return ($k?->getKasLalu() ?? 0) + ($k?->getMasukGuest() ?? 0) + (int) $masuk - (int) $keluar;
    }

    /**
     * Pisahkan pengeluaran yang diambil dari kas minggu lalu (beli air, kok)
     * dari yang dibayar pakai uang hari itu (parkir, lainnya).
     *
     * @param Pengeluaran[] $pengeluaran
     * @return array{0: Pengeluaran[], 1: Pengeluaran[]} [dari kas lalu, dari uang hari ini]
     */
    public static function pisahKasLalu(array $pengeluaran, int $kasLalu): array
    {
        $dariKasLalu = static fn ($p) => Pengeluaran::dariKasLalu($p->getKategori());
        $dariKas = array_values(array_filter($pengeluaran, $dariKasLalu));
        $hariIni = array_values(array_filter($pengeluaran, static fn ($p) => ! $dariKasLalu($p)));

        return [$dariKas, $hariIni];
    }

    /** Hapus semua catatan satu tanggal main: yang main, pengeluaran, guest & kas minggu lalu. */
    public function hapusHari(DateTimeImmutable $tanggal): void
    {
        $em = Doctrine::em();
        $tgl = $tanggal->format('Y-m-d');
        $em->wrapInTransaction(static function ($em) use ($tgl) {
            $em->createQuery('DELETE FROM App\Entities\Pembayaran p WHERE p.tanggalBayar = :tgl')->setParameter('tgl', $tgl)->execute();
            $em->createQuery('DELETE FROM App\Entities\Pengeluaran p WHERE p.tanggal = :tgl')->setParameter('tgl', $tgl)->execute();
            $em->createQuery('DELETE FROM App\Entities\KasHari k WHERE k.tanggal = :tgl')->setParameter('tgl', $tgl)->execute();
        });
        $em->clear();
    }

    public function kasHari(DateTimeImmutable $tanggal): ?KasHari
    {
        return Doctrine::em()->getRepository(KasHari::class)->findOneBy(['tanggal' => $tanggal]);
    }

    /** Ubah catatan tanggal itu (dibuat kalau belum ada, dihapus kalau jadi kosong). */
    private function ubahKasHari(DateTimeImmutable $tanggal, callable $ubah): void
    {
        $em = Doctrine::em();
        $k = $this->kasHari($tanggal);
        if ($k === null) {
            $k = new KasHari();
            $k->setTanggal($tanggal);
        }
        $ubah($k);
        if ($k->kosong()) {
            if ($k->getId() !== null) {
                $em->remove($k);
            }
        } elseif ($k->getId() === null) {
            $em->persist($k);
        }
        $em->flush();
    }

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

    /** @return Pengeluaran[] pengeluaran pada satu hari main. */
    public function daftarPengeluaranTanggal(DateTimeImmutable $tanggal): array
    {
        return Doctrine::em()
            ->createQuery('SELECT p FROM App\Entities\Pengeluaran p WHERE p.tanggal = :tgl ORDER BY p.id ASC')
            ->setParameter('tgl', $tanggal->format('Y-m-d'))
            ->getResult();
    }

    /**
     * Menyimpan pengeluaran satu hari main sekaligus: satu nominal per jenis.
     * Nominal diisi = tambah/ubah, dikosongkan (0) = hapus.
     *
     * @param array<string, array{jumlah: int, keterangan: ?string}> $isian kategori => isian
     */
    public function simpanPengeluaranHari(DateTimeImmutable $tanggal, array $isian): void
    {
        $em = Doctrine::em();
        $lama = [];
        foreach ($this->daftarPengeluaranTanggal($tanggal) as $p) {
            $lama[Pengeluaran::kunci($p->getKategori())][] = $p;
        }
        foreach ($isian as $kategori => $data) {
            $ada = $lama[Pengeluaran::kunci($kategori)] ?? [];
            // Kalau dulu tercatat lebih dari sekali, disatukan jadi satu baris
            foreach (array_slice($ada, 1) as $p) {
                $em->remove($p);
            }
            $p = $ada[0] ?? null;
            if ($data['jumlah'] <= 0) {
                if ($p !== null) {
                    $em->remove($p);
                }

                continue;
            }
            if ($p === null) {
                $p = new Pengeluaran();
                $p->setTanggal($tanggal);
                $p->setKategori($kategori);
                $em->persist($p);
            }
            $p->setKategori($kategori);
            $p->setJumlah($data['jumlah']);
            $p->setKeterangan($data['keterangan']);
        }
        $em->flush();
    }

    /** Pemasukan dihitung dari tanggal main; bulan kosong = semua waktu. */
    public function totalPemasukan(string $bulan = ''): int
    {
        return $this->total('SELECT COALESCE(SUM(p.jumlah), 0) FROM App\Entities\Pembayaran p'
                . ($bulan !== '' ? ' WHERE p.tanggalBayar BETWEEN :awal AND :akhir' : ''), $bulan)
            + $this->total('SELECT COALESCE(SUM(k.guest * k.tarifGuest), 0) FROM App\Entities\KasHari k'
                . ($bulan !== '' ? ' WHERE k.tanggal BETWEEN :awal AND :akhir' : ''), $bulan);
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
        foreach ((new PembayaranModel())->daftar($bulan) as $p) {
            if ($p->getJumlah() <= 0) {
                continue; // seluruh iurannya memotong talangan, tidak ada uang masuk
            }
            $hasil[] = [
                'tanggal'    => $p->getTanggalBayar(),
                'pemasukan'  => true,
                'keterangan' => 'Main ' . $p->getAnggota()->getNama() . ($p->getPotongTalangan() > 0 ? ' (sebagian potong talangan)' : ''),
                'jumlah'     => $p->getJumlah(),
            ];
        }
        foreach ($this->daftarGuest($bulan) as $k) {
            $hasil[] = [
                'tanggal'    => $k->getTanggal(),
                'pemasukan'  => true,
                'keterangan' => 'Guest ' . $k->getGuest() . ' orang (' . $k->getGuest() . ' x ' . angka($k->getTarifGuest()) . ')',
                'jumlah'     => $k->getMasukGuest(),
            ];
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
            [$awal, $akhir] = rentang_bulan($bulan);
            $query->setParameter('awal', $awal)->setParameter('akhir', $akhir);
        }
    }
}
