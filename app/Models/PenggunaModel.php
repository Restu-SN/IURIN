<?php

namespace App\Models;

use App\Entities\Pengguna;
use App\Libraries\Doctrine;
use App\Libraries\Password;

class PenggunaModel
{
    /** Mengembalikan pengguna jika username & password cocok, selain itu null. */
    public function login(string $username, string $password): ?Pengguna
    {
        $pengguna = Doctrine::em()->getRepository(Pengguna::class)->findOneBy(['username' => $username]);

        if ($pengguna !== null && Password::verify($password, $pengguna->getPasswordHash())) {
            return $pengguna;
        }

        return null;
    }

    public function cariId(int $id): ?Pengguna
    {
        return Doctrine::em()->find(Pengguna::class, $id);
    }

    public function gantiPassword(int $id, string $passwordBaru): void
    {
        $em = Doctrine::em();
        $em->find(Pengguna::class, $id)->setPasswordHash(Password::hash($passwordBaru));
        $em->flush();
    }
}
