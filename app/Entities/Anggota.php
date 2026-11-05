<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'anggota')]
class Anggota
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private string $nama = '';

    #[ORM\Column(name: 'nominal_iuran', type: 'integer')]
    private int $nominalIuran = 0;

    public function getId(): ?int { return $this->id; }

    public function getNama(): string { return $this->nama; }
    public function setNama(string $nama): void { $this->nama = $nama; }

    public function getNominalIuran(): int { return $this->nominalIuran; }
    public function setNominalIuran(int $nominal): void { $this->nominalIuran = $nominal; }
}
