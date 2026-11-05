# Iurin — Iuran Mingguan PB Orang Pusing

Aplikasi web untuk mencatat iuran mingguan anggota klub badminton **PB Orang Pusing**:
siapa yang sudah bayar, siapa yang belum/menunggak, dan rekap kas klub.

Dibuat dengan **Java (JSP + Servlet)**, **Hibernate (ORM)**, **Maven**, **MySQL**, dan **Bootstrap 5**, dijalankan di **Apache Tomcat** melalui **NetBeans**.

## Fitur

- Login bendahara (password disimpan sebagai hash PBKDF2)
- Kelola anggota: tambah, lihat, cari, edit, hapus
- Catat pembayaran iuran per minggu (pembayaran ganda di minggu yang sama ditolak)
- Status iuran per minggu: Lunas / Belum bayar / Menunggak
- Rekap kas: pemasukan, pengeluaran, dan saldo
- Tampilan responsif (laptop & HP)

## Struktur Project (MVC)

```
iurin/
├── database/
│   └── iurin.sql                  ← script membuat database + data anggota
├── pom.xml                        ← konfigurasi Maven
└── src/main/
    ├── java/com/iurin/
    │   ├── model/                 ← MODEL: entity Hibernate & DAO (HQL)
    │   │   ├── Anggota.java, Pembayaran.java, Pengeluaran.java, ...
    │   │   └── AnggotaDao.java, PembayaranDao.java, KasDao.java, AdminDao.java
    │   ├── controller/            ← CONTROLLER: Servlet yang menerima request
    │   │   └── AnggotaServlet.java, PembayaranServlet.java, StatusServlet.java, ...
    │   └── util/                  ← HibernateUtil, hash password, format rupiah
    ├── resources/hibernate.cfg.xml ← konfigurasi Hibernate & koneksi MySQL
    └── webapp/
        ├── WEB-INF/views/         ← VIEW: halaman JSP
        ├── WEB-INF/web.xml        ← nama klub & konfigurasi aplikasi
        └── assets/                ← CSS dan foto latar login
```

## Cara Menjalankan

1. **XAMPP**: Start **MySQL** (Tomcat XAMPP tidak perlu di-Start).
2. **Database**: buka `http://localhost/phpmyadmin` → tab **Import** → pilih `database/iurin.sql` → **Import**.
3. **NetBeans**: aktifkan plugin **Java Web and EE**, lalu daftarkan server
   **Services → Servers → Add Server → Apache Tomcat** dengan lokasi `C:\xampp\tomcat`.
4. **File → Open Project** → pilih folder `iurin` → klik kanan project → **Run**.
5. Buka `http://localhost:8080/iurin` dan login dengan username `admin`, password `admin123`
   (segera ganti lewat menu **Ganti password**).

Jika MySQL memakai password, ubah `hibernate.connection.password` di `src/main/resources/hibernate.cfg.xml`.
