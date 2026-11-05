<?php

namespace App\Filters;

use App\Entities\Pengguna;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/** Hanya admin yang boleh mengubah data; akun anggota cuma bisa melihat. */
class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if ((session('pengguna')['role'] ?? null) !== Pengguna::ADMIN) {
            return redirect()->back()->with('flash', 'Akun Anda hanya bisa melihat data.')->with('flashTipe', 'warning');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
