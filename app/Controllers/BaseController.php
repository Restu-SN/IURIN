<?php

namespace App\Controllers;

use App\Entities\Pengguna;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $this->helpers = ['iurin', 'form', 'url'];

        parent::initController($request, $response, $logger);
    }

    /** Menampilkan halaman beserta header & footer bersama. */
    protected function tampil(string $view, array $data, string $menu): string
    {
        $data['menu'] = $menu;
        $data['klub'] = config('Iurin')->namaKlub;
        $data['pengguna'] = session('pengguna');
        $data['isAdmin'] = ($data['pengguna']['role'] ?? null) === Pengguna::ADMIN;

        return view('layout/header', $data) . view($view, $data) . view('layout/footer', $data);
    }

    protected function flash(string $pesan, string $tipe = 'success'): void
    {
        session()->setFlashdata(['flash' => $pesan, 'flashTipe' => $tipe]);
    }

    protected function angkaPost(string $nama): int
    {
        return (int) str_replace('.', '', (string) $this->request->getPost($nama));
    }

    protected function teksPost(string $nama): string
    {
        return trim((string) $this->request->getPost($nama));
    }
}
