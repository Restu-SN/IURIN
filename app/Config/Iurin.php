<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Iurin extends BaseConfig
{
    /** Nama klub yang tampil di header, login, dan judul tab. */
    public string $namaKlub = 'PB Orang Pusing';

    /** Naikkan setiap mengubah iurin.css supaya browser tidak memakai versi lama dari cache. */
    public string $versiAset = '1';
}
