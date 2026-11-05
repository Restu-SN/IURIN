# Iurin — Iuran Mingguan PB Orang Pusing

Aplikasi web untuk mencatat iuran mingguan anggota klub badminton **PB Orang Pusing**:
siapa yang sudah bayar, siapa yang belum/menunggak, dan rekap kas klub.

Dibuat dengan **PHP 8.2+**, **CodeIgniter 4** (MVC), **Doctrine ORM**, **MySQL**, dan **Bootstrap 5**.

> Versi sebelumnya (Java Servlet/JSP + Hibernate) disimpan di branch [`java`](../../tree/java).

## Fitur

- Login dengan dua jenis akun:
  - **admin** — boleh menambah, mengubah, dan menghapus data
  - **anggota** — hanya bisa melihat
- Kelola anggota: tambah, lihat, cari, edit, hapus
- Catat pembayaran iuran per minggu (pembayaran ganda di minggu yang sama ditolak)
- Status iuran per minggu: Lunas / Belum bayar / Menunggak
- Rekap kas: pemasukan, pengeluaran, dan saldo
- Password disimpan sebagai hash PBKDF2, form dilindungi CSRF
- Tampilan responsif (laptop & HP)

## Struktur Project (MVC)

```
iurin/
├── app/
│   ├── Entities/       ← entity Doctrine (Anggota, Pembayaran, Pengeluaran, Pengguna)
│   ├── Models/         ← MODEL: query DQL lewat EntityManager Doctrine
│   ├── Controllers/    ← CONTROLLER: Auth, Anggota, Pembayaran, Status, Kas, Password
│   ├── Views/          ← VIEW: halaman tampilan
│   ├── Filters/        ← LoginFilter (wajib login), AdminFilter (khusus admin)
│   ├── Libraries/      ← Doctrine.php (EntityManager), Password.php (hash)
│   ├── Helpers/        ← format rupiah & minggu
│   └── Config/         ← Routes.php, Iurin.php (nama klub)
├── public/             ← index.php, assets/ (CSS, foto login)
├── database/iurin.sql  ← script database
└── composer.json       ← CodeIgniter 4, Doctrine ORM
```

## Cara Menjalankan (XAMPP)

1. Taruh folder ini di `C:\xampp\htdocs\iurin`, aktifkan `extension=intl` di `php.ini`, lalu Start **Apache** dan **MySQL**.
2. Jalankan `composer install` di folder project.
3. Import `database/iurin.sql` lewat phpMyAdmin.
4. Salin file `env` menjadi `.env`, lalu isi `app.baseURL` dan `database.default.*`.
5. Buka `http://localhost/iurin`.

Akun awal: **admin** / `admin123` dan **anggota** / `pusing2026` — segera ganti lewat menu **Ganti password**.
