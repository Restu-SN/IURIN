CREATE DATABASE IF NOT EXISTS iurin CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE iurin;

CREATE TABLE pengguna (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nama          VARCHAR(100) NOT NULL,
    role          VARCHAR(10)  NOT NULL DEFAULT 'anggota'
);

CREATE TABLE anggota (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nama          VARCHAR(100) NOT NULL UNIQUE,
    nominal_iuran INT          NOT NULL DEFAULT 0
);

CREATE TABLE pembayaran (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    anggota_id    INT         NOT NULL,
    minggu        CHAR(8)     NOT NULL,
    jumlah        INT         NOT NULL,
    tanggal_bayar DATE        NOT NULL,
    keterangan    VARCHAR(255),
    UNIQUE KEY uk_anggota_minggu (anggota_id, minggu),
    CONSTRAINT fk_pembayaran_anggota FOREIGN KEY (anggota_id)
        REFERENCES anggota (id) ON DELETE CASCADE
);

CREATE TABLE pengeluaran (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    tanggal    DATE         NOT NULL,
    kategori   VARCHAR(50)  NOT NULL,
    keterangan VARCHAR(255),
    jumlah     INT          NOT NULL
);

INSERT INTO pengguna (username, password_hash, nama, role) VALUES
('admin', '65536:glcm/l74HPil3fYt+H3Oqg==:2AbNTdXvB8iBxUNETwzg6xgAH0M5m7yO450cdg/FJA4=', 'admin', 'admin'),
('user',  '65536:li46sO0g4M3XiP9RtF9Vkw==:OniywFLMcZxb1cgz0rFYi+8dZOQ18dqKr0Zx22yKu+E=', 'user', 'anggota');

INSERT INTO anggota (nama, nominal_iuran) VALUES
('Etuuu',       15000),
('Mas Moko',    15000),
('Mas Dela',    15000),
('Bang Botak',  15000),
('Cang Aldi',   15000),
('Pito',        15000),
('Bang Yuspi',  15000),
('Bang Ucup',   15000),
('Bang Awan',   15000),
('Om Zirin',    15000),
('Bang Agil',   15000),
('Bang Gilang', 15000);
