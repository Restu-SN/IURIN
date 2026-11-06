-- Pembayaran dicatat per tanggal main (yang datang bayar), bukan per minggu.
-- Jalankan sekali di database yang sudah ada (tab SQL phpMyAdmin).

ALTER TABLE pembayaran ADD UNIQUE KEY uk_anggota_tanggal (anggota_id, tanggal_bayar);
ALTER TABLE pembayaran DROP INDEX uk_anggota_minggu;
ALTER TABLE pembayaran DROP COLUMN minggu;
