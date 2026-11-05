<?php

namespace App\Controllers;

use App\Models\PenggunaModel;

class Auth extends BaseController
{
    public function index()
    {
        if (session()->has('pengguna')) {
            return redirect()->to('anggota');
        }

        return view('login', ['klub' => config('Iurin')->namaKlub]);
    }

    public function login()
    {
        $pengguna = (new PenggunaModel())->login(
            $this->teksPost('username'),
            (string) $this->request->getPost('password'),
        );

        if ($pengguna === null) {
            return view('login', [
                'klub'     => config('Iurin')->namaKlub,
                'error'    => 'Username atau password salah.',
                'username' => $this->teksPost('username'),
            ]);
        }

        session()->regenerate(true);
        session()->set('pengguna', [
            'id'       => $pengguna->getId(),
            'username' => $pengguna->getUsername(),
            'nama'     => $pengguna->getNama(),
            'role'     => $pengguna->getRole(),
        ]);

        return redirect()->to('anggota');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('login');
    }
}
