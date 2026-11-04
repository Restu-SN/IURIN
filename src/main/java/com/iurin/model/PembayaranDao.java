package com.iurin.model;

import com.iurin.util.Format;
import com.iurin.util.Koneksi;
import com.iurin.util.ValidasiException;
import java.sql.Connection;
import java.sql.Date;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

public class PembayaranDao {

    /** Riwayat pembayaran; minggu null/kosong = semua minggu. */
    public List<Pembayaran> daftar(String minggu) throws SQLException {
        boolean filter = minggu != null && !minggu.isEmpty();
        String sql = "SELECT p.*, a.nama FROM pembayaran p JOIN anggota a ON a.id = p.anggota_id"
                + (filter ? " WHERE p.minggu = ?" : "")
                + " ORDER BY p.tanggal_bayar DESC, p.id DESC";
        try (Connection c = Koneksi.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            if (filter) {
                ps.setString(1, minggu);
            }
            List<Pembayaran> hasil = new ArrayList<>();
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) {
                    Pembayaran p = dariRow(rs);
                    p.setNamaAnggota(rs.getString("nama"));
                    hasil.add(p);
                }
            }
            return hasil;
        }
    }

    public void tambah(Pembayaran p) throws SQLException, ValidasiException {
        try (Connection c = Koneksi.getConnection()) {
            try (PreparedStatement cek = c.prepareStatement(
                    "SELECT COUNT(*) FROM pembayaran WHERE anggota_id = ? AND minggu = ?")) {
                cek.setInt(1, p.getAnggotaId());
                cek.setString(2, p.getMinggu());
                try (ResultSet rs = cek.executeQuery()) {
                    rs.next();
                    if (rs.getInt(1) > 0) {
                        throw new ValidasiException("Anggota ini sudah membayar iuran "
                                + Format.namaMinggu(p.getMinggu()) + ".");
                    }
                }
            }
            String sql = "INSERT INTO pembayaran (anggota_id, minggu, jumlah, tanggal_bayar, keterangan) VALUES (?, ?, ?, ?, ?)";
            try (PreparedStatement ps = c.prepareStatement(sql)) {
                ps.setInt(1, p.getAnggotaId());
                ps.setString(2, p.getMinggu());
                ps.setInt(3, p.getJumlah());
                ps.setDate(4, Date.valueOf(p.getTanggalBayar()));
                ps.setString(5, p.getKeterangan());
                ps.executeUpdate();
            }
        }
    }

    public void hapus(int id) throws SQLException {
        try (Connection c = Koneksi.getConnection();
                PreparedStatement ps = c.prepareStatement("DELETE FROM pembayaran WHERE id = ?")) {
            ps.setInt(1, id);
            ps.executeUpdate();
        }
    }

    /**
     * Status iuran semua anggota pada minggu tersebut.
     * Lunas = ada pembayaran; Menunggak = belum bayar dan minggunya sudah lewat;
     * Belum Bayar = belum bayar untuk minggu berjalan / mendatang.
     */
    public List<StatusIuran> statusMinggu(String minggu) throws SQLException {
        String sql = "SELECT a.*, p.id AS p_id, p.anggota_id, p.minggu, p.jumlah, p.tanggal_bayar, p.keterangan"
                + " FROM anggota a LEFT JOIN pembayaran p ON p.anggota_id = a.id AND p.minggu = ?"
                + " ORDER BY a.nama";
        // Format YYYY-Www bisa dibandingkan langsung sebagai teks
        boolean sudahLewat = minggu.compareTo(Format.mingguIni()) < 0;
        try (Connection c = Koneksi.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setString(1, minggu);
            List<StatusIuran> hasil = new ArrayList<>();
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) {
                    Anggota a = AnggotaDao.dariRow(rs);
                    if (rs.getObject("p_id") != null) {
                        Pembayaran p = dariRow(rs);
                        p.setId(rs.getInt("p_id"));
                        hasil.add(new StatusIuran(a, StatusIuran.Status.LUNAS, p));
                    } else {
                        hasil.add(new StatusIuran(a, sudahLewat
                                ? StatusIuran.Status.MENUNGGAK : StatusIuran.Status.BELUM_BAYAR, null));
                    }
                }
            }
            return hasil;
        }
    }

    private static Pembayaran dariRow(ResultSet rs) throws SQLException {
        Pembayaran p = new Pembayaran();
        p.setId(rs.getInt("id"));
        p.setAnggotaId(rs.getInt("anggota_id"));
        p.setMinggu(rs.getString("minggu"));
        p.setJumlah(rs.getInt("jumlah"));
        p.setTanggalBayar(rs.getDate("tanggal_bayar").toLocalDate());
        p.setKeterangan(rs.getString("keterangan"));
        return p;
    }
}
