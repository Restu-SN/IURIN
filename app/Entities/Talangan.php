<?php

namespace App\Entities;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Uang klub yang ditalangin (dibayari dulu) oleh anggota, mis. sewa lapangan.
 * Dilunasi pelan-pelan: tiap anggota itu main, iurannya memotong talangan.
 */
#[ORM\Entity]
#[ORM\Table(name: 'talangan')]
class Talangan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Anggota::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'anggota_id', nullable: false, onDelete: 'CASCADE')]
    private Anggota $anggota;

    #[ORM\Column(type: 'date_immutable')]
    private DateTimeImmutable $tanggal;

    #[ORM\Column(type: 'integer')]
    private int $jumlah = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $keterangan = null;

    public function getId(): ?int { return $this->id; }

    public function getAnggota(): Anggota { return $this->anggota; }
    public function setAnggota(Anggota $anggota): void { $this->anggota = $anggota; }

    public function getTanggal(): DateTimeImmutable { return $this->tanggal; }
    public function setTanggal(DateTimeImmutable $tanggal): void { $this->tanggal = $tanggal; }

    public function getJumlah(): int { return $this->jumlah; }
    public function setJumlah(int $jumlah): void { $this->jumlah = $jumlah; }

    public function getKeterangan(): ?string { return $this->keterangan; }
    public function setKeterangan(?string $keterangan): void { $this->keterangan = $keterangan; }
}
