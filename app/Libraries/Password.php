<?php

namespace App\Libraries;

/**
 * Hash password PBKDF2-HMAC-SHA256. Format tersimpan: iterasi:salt:hash (Base64),
 * sama dengan versi Java sehingga password lama tetap berlaku.
 */
class Password
{
    private const ITERASI = 65536;

    public static function hash(string $password): string
    {
        $salt = random_bytes(16);
        $hash = hash_pbkdf2('sha256', $password, $salt, self::ITERASI, 32, true);

        return self::ITERASI . ':' . base64_encode($salt) . ':' . base64_encode($hash);
    }

    public static function verify(string $password, string $tersimpan): bool
    {
        $bagian = explode(':', $tersimpan);
        if (count($bagian) !== 3) {
            return false;
        }
        [$iterasi, $salt, $hash] = $bagian;
        $baru = hash_pbkdf2('sha256', $password, base64_decode($salt), (int) $iterasi, 32, true);

        return hash_equals(base64_decode($hash), $baru);
    }
}
