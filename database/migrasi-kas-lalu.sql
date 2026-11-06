-- Kas minggu lalu & guest (orang di luar anggota) per tanggal main.
-- Jalankan sekali di database yang sudah ada (tab SQL phpMyAdmin).

CREATE TABLE IF NOT EXISTS kas_hari (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    tanggal     DATE NOT NULL,
    kas_minggu_lalu INT NOT NULL DEFAULT 0,
    guest       INT  NOT NULL DEFAULT 0,
    tarif_guest INT  NOT NULL DEFAULT 15000,
    UNIQUE KEY uk_kas_hari_tanggal (tanggal)
);
