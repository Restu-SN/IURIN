<?php

namespace App\Controllers;

use App\Models\PenggunaModel;

class Password extends BaseController
{
    public function index(array $data = [])
    {
        return $this->tampil('password', $data, '');
    }

    public function simpan()
    {
        $model = new PenggunaModel();
        $pengguna = session('pengguna');
        $baru = (string) $this->request->getPost('baru');

        if ($model->login($pengguna['username'], (string) $this->request->getPost('lama')) === null) {
            return $this->index(['error' => 'Password lama salah.']);
        }
        if (strlen($baru) < 6 || $baru !== $this->request->getPost('ulang')) {
            return $this->index(['error' => 'Password baru minimal 6 karakter dan harus sama dengan konfirmasi.']);
        }

        $model->gantiPassword($pengguna['id'], $baru);
        $this->flash('Password berhasil diganti.');

        return redirect()->to('anggota');
    }
}
