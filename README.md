# Iurin — Kas & Iuran Main PB Orang Pusing

Aplikasi web untuk mencatat kas klub badminton **PB Orang Pusing**: siapa yang datang main
(yang datang bayar, yang tidak datang tidak bayar), guest, kas minggu lalu, pengeluaran hari main,
talangan anggota, dan struk per hari main.

Dibuat dengan **Java (JSP + Servlet)**, **Hibernate (ORM)**, **Maven**, **MySQL**, dan **Bootstrap 5**, dijalankan di **Apache Tomcat** melalui **NetBeans**.
Versi PHP (CodeIgniter 4 + Doctrine) dengan fitur yang sama ada di branch `main` dan dipakai di hosting.

## Fitur

- Login dua peran: **admin** (boleh mengubah data) dan **anggota** (hanya melihat); password disimpan sebagai hash PBKDF2
- Kelola anggota: tambah, lihat, cari, edit, hapus
- **Catat Main**: centang anggota yang datang, iuran otomatis tercatat; **guest** cukup jumlah orang × tarif
- **Kas minggu lalu**: uang bawaan dipotong Beli Air & Pembelian Kok; pengeluaran lain (parkir, lainnya) dari uang hari itu
- **Talangan**: uang klub yang dibayari dulu oleh anggota (mis. sewa lapangan) otomatis terpotong setiap anggota itu main
- **Riwayat Main**: ringkasan per tanggal dan per anggota dalam sebulan
- **Rekap Kas**: pemasukan, pengeluaran, saldo, daftar transaksi, dan **struk** per hari main (cetak, unduh JPG/PDF, hapus)
- Kolom nominal otomatis memakai titik ribuan (10.000); tampilan responsif (laptop & HP)

## Struktur Project (MVC)

```
iurin/
├── database/
│   └── iurin.sql                  ← script membuat database + data awal
├── pom.xml                        ← konfigurasi Maven
└── src/main/
    ├── java/com/iurin/
    │   ├── model/                 ← MODEL: entity Hibernate & DAO (HQL)
    │   │   ├── Pengguna, Anggota, Pembayaran, Pengeluaran, KasHari, Talangan
    │   │   └── PenggunaDao, AnggotaDao, PembayaranDao, KasDao, TalanganDao
    │   ├── controller/            ← CONTROLLER: Servlet & filter login/hak akses
    │   │   └── AnggotaServlet, PembayaranServlet (Catat Main), StatusServlet (Riwayat), KasServlet, ...
    │   └── util/                  ← HibernateUtil, hash password, format rupiah & tanggal
    ├── resources/hibernate.cfg.xml ← konfigurasi Hibernate & koneksi MySQL
    └── webapp/
        ├── WEB-INF/views/         ← VIEW: halaman JSP
        ├── WEB-INF/iurin.tld      ← fungsi EL untuk JSP (rupiah, tanggal)
        ├── WEB-INF/web.xml        ← nama klub & konfigurasi aplikasi
        └── assets/                ← CSS, logo struk, dan foto latar login
```

## Cara Menjalankan

1. **XAMPP**: Start **MySQL** (Tomcat XAMPP tidak perlu di-Start).
2. **Database**: buka `http://localhost/phpmyadmin` → tab **Import** → pilih `database/iurin.sql` → **Import**.
   Database ini sama dengan yang dipakai versi PHP, jadi kalau database `iurin` sudah ada, langkah ini dilewati.
3. **NetBeans**: aktifkan plugin **Java Web and EE**, lalu daftarkan server
   **Services → Servers → Add Server → Apache Tomcat** dengan lokasi `C:\xampp\tomcat`.
4. **File → Open Project** → pilih folder `iurin` → klik kanan project → **Run**.
5. Buka `http://localhost:8080/iurin` dan login dengan akun admin (`admin`) atau akun anggota (`user`, hanya lihat).

Jika MySQL memakai password, ubah `hibernate.connection.password` di `src/main/resources/hibernate.cfg.xml`.
