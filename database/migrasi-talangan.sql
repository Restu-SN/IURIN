-- Talangan: uang klub yang dibayari dulu oleh anggota (mis. sewa lapangan).
-- Tiap anggota itu main, iurannya memotong talangan (pembayaran.potong_talangan).
-- Jalankan sekali di database yang sudah ada (tab SQL phpMyAdmin).

CREATE TABLE IF NOT EXISTS talangan (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    anggota_id INT          NOT NULL,
    tanggal    DATE         NOT NULL,
    jumlah     INT          NOT NULL,
    keterangan VARCHAR(255),
    KEY idx_talangan_anggota (anggota_id)
);

ALTER TABLE pembayaran ADD COLUMN IF NOT EXISTS potong_talangan INT NOT NULL DEFAULT 0;
