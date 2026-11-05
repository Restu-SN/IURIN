<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'pengguna')]
class Pengguna
{
    public const ADMIN = 'admin';
    public const ANGGOTA = 'anggota';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private string $username;

    #[ORM\Column(name: 'password_hash', length: 255)]
    private string $passwordHash;

    #[ORM\Column(length: 100)]
    private string $nama;

    /** admin = boleh mengubah data, anggota = hanya melihat. */
    #[ORM\Column(length: 10)]
    private string $role = self::ANGGOTA;

    public function getId(): ?int { return $this->id; }

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): void { $this->username = $username; }

    public function getPasswordHash(): string { return $this->passwordHash; }
    public function setPasswordHash(string $hash): void { $this->passwordHash = $hash; }

    public function getNama(): string { return $this->nama; }
    public function setNama(string $nama): void { $this->nama = $nama; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void { $this->role = $role; }

    public function isAdmin(): bool { return $this->role === self::ADMIN; }
}
