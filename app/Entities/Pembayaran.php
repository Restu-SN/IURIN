<?php

namespace App\Entities;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'pembayaran')]
#[ORM\UniqueConstraint(name: 'uk_anggota_tanggal', columns: ['anggota_id', 'tanggal_bayar'])]
/** Satu anggota yang datang main pada satu tanggal = satu pembayaran. */
class Pembayaran
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Anggota::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'anggota_id', nullable: false, onDelete: 'CASCADE')]
    private Anggota $anggota;

    /** Uang tunai yang masuk kas (iuran dikurangi potongan talangan). */
    #[ORM\Column(type: 'integer')]
    private int $jumlah;

    /** Bagian iuran yang dipakai untuk memotong talangan anggota ini. */
    #[ORM\Column(name: 'potong_talangan', type: 'integer')]
    private int $potongTalangan = 0;

    #[ORM\Column(name: 'tanggal_bayar', type: 'date_immutable')]
    private DateTimeImmutable $tanggalBayar;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $keterangan = null;

    public function getId(): ?int { return $this->id; }

    public function getAnggota(): Anggota { return $this->anggota; }
    public function setAnggota(Anggota $anggota): void { $this->anggota = $anggota; }

    public function getJumlah(): int { return $this->jumlah; }
    public function setJumlah(int $jumlah): void { $this->jumlah = $jumlah; }

    public function getPotongTalangan(): int { return $this->potongTalangan; }
    public function setPotongTalangan(int $potong): void { $this->potongTalangan = $potong; }

    /** Iuran sekali main sebelum dipotong talangan. */
    public function getIuran(): int { return $this->jumlah + $this->potongTalangan; }

    public function getTanggalBayar(): DateTimeImmutable { return $this->tanggalBayar; }
    public function setTanggalBayar(DateTimeImmutable $tanggal): void { $this->tanggalBayar = $tanggal; }

    public function getKeterangan(): ?string { return $this->keterangan; }
    public function setKeterangan(?string $keterangan): void { $this->keterangan = $keterangan; }
}
