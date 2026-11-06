<?php

namespace App\Entities;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Catatan tambahan satu tanggal main: kas minggu lalu (uang bawaan) dan
 * guest tanpa nama (jumlah orang x tarif) yang ikut bayar.
 */
#[ORM\Entity]
#[ORM\Table(name: 'kas_hari')]
#[ORM\UniqueConstraint(name: 'uk_kas_hari_tanggal', columns: ['tanggal'])]
class KasHari
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'date_immutable')]
    private DateTimeImmutable $tanggal;

    #[ORM\Column(name: 'kas_minggu_lalu', type: 'integer')]
    private int $kasLalu = 0;

    /** Jumlah guest / orang lain yang main tanpa dicatat namanya. */
    #[ORM\Column(type: 'integer')]
    private int $guest = 0;

    #[ORM\Column(name: 'tarif_guest', type: 'integer')]
    private int $tarifGuest = 15000;

    public function getId(): ?int { return $this->id; }

    public function getTanggal(): DateTimeImmutable { return $this->tanggal; }
    public function setTanggal(DateTimeImmutable $tanggal): void { $this->tanggal = $tanggal; }

    public function getKasLalu(): int { return $this->kasLalu; }
    public function setKasLalu(int $kasLalu): void { $this->kasLalu = $kasLalu; }

    public function getGuest(): int { return $this->guest; }
    public function setGuest(int $guest): void { $this->guest = $guest; }

    public function getTarifGuest(): int { return $this->tarifGuest; }
    public function setTarifGuest(int $tarif): void { $this->tarifGuest = $tarif; }

    /** Uang masuk dari guest: jumlah orang x tarif. */
    public function getMasukGuest(): int { return $this->guest * $this->tarifGuest; }

    /** Tidak ada isinya lagi, boleh dihapus. */
    public function kosong(): bool { return $this->kasLalu <= 0 && $this->guest <= 0; }
}
