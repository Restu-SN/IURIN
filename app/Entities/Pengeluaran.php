<?php

namespace App\Entities;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'pengeluaran')]
class Pengeluaran
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'date_immutable')]
    private DateTimeImmutable $tanggal;

    #[ORM\Column(length: 50)]
    private string $kategori;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $keterangan = null;

    #[ORM\Column(type: 'integer')]
    private int $jumlah;

    public function getId(): ?int { return $this->id; }

    public function getTanggal(): DateTimeImmutable { return $this->tanggal; }
    public function setTanggal(DateTimeImmutable $tanggal): void { $this->tanggal = $tanggal; }

    public function getKategori(): string { return $this->kategori; }
    public function setKategori(string $kategori): void { $this->kategori = $kategori; }

    public function getKeterangan(): ?string { return $this->keterangan; }
    public function setKeterangan(?string $keterangan): void { $this->keterangan = $keterangan; }

    public function getJumlah(): int { return $this->jumlah; }
    public function setJumlah(int $jumlah): void { $this->jumlah = $jumlah; }
}
