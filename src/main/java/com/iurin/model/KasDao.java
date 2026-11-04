package com.iurin.model;

import com.iurin.util.Koneksi;
import java.sql.Connection;
import java.sql.Date;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

/** Pengeluaran klub dan rekap kas (pemasukan dari iuran, pengeluaran, saldo). */
public class KasDao {

    public void tambahPengeluaran(Pengeluaran p) throws SQLException {
        String sql = "INSERT INTO pengeluaran (tanggal, kategori, keterangan, jumlah) VALUES (?, ?, ?, ?)";
        try (Connection c = Koneksi.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setDate(1, Date.valueOf(p.getTanggal()));
            ps.setString(2, p.getKategori());
            ps.setString(3, p.getKeterangan());
            ps.setInt(4, p.getJumlah());
            ps.executeUpdate();
        }
    }

    public void hapusPengeluaran(int id) throws SQLException {
        try (Connection c = Koneksi.getConnection();
                PreparedStatement ps = c.prepareStatement("DELETE FROM pengeluaran WHERE id = ?")) {
            ps.setInt(1, id);
            ps.executeUpdate();
        }
    }

    public List<Pengeluaran> daftarPengeluaran(String bulan) throws SQLException {
        boolean filter = adaFilter(bulan);
        String sql = "SELECT * FROM pengeluaran"
                + (filter ? " WHERE DATE_FORMAT(tanggal, '%Y-%m') = ?" : "")
                + " ORDER BY tanggal DESC, id DESC";
        try (Connection c = Koneksi.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            if (filter) {
                ps.setString(1, bulan);
            }
            List<Pengeluaran> hasil = new ArrayList<>();
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) {
                    Pengeluaran p = new Pengeluaran();
                    p.setId(rs.getInt("id"));
                    p.setTanggal(rs.getDate("tanggal").toLocalDate());
                    p.setKategori(rs.getString("kategori"));
                    p.setKeterangan(rs.getString("keterangan"));
                    p.setJumlah(rs.getInt("jumlah"));
                    hasil.add(p);
                }
            }
            return hasil;
        }
    }

    /** Pemasukan dihitung dari tanggal bayar iuran; bulan kosong = semua waktu. */
    public long totalPemasukan(String bulan) throws SQLException {
        return total("SELECT COALESCE(SUM(jumlah), 0) FROM pembayaran"
                + (adaFilter(bulan) ? " WHERE DATE_FORMAT(tanggal_bayar, '%Y-%m') = ?" : ""), bulan);
    }

    public long totalPengeluaran(String bulan) throws SQLException {
        return total("SELECT COALESCE(SUM(jumlah), 0) FROM pengeluaran"
                + (adaFilter(bulan) ? " WHERE DATE_FORMAT(tanggal, '%Y-%m') = ?" : ""), bulan);
    }

    /** Gabungan pemasukan dan pengeluaran, terbaru di atas. */
    public List<Transaksi> transaksi(String bulan) throws SQLException {
        boolean filter = adaFilter(bulan);
        String sql = "SELECT * FROM ("
                + " SELECT p.tanggal_bayar AS tanggal, 1 AS masuk,"
                + "   CONCAT('Iuran ', a.nama, ' (', p.minggu, ')') AS ket, p.jumlah"
                + "   FROM pembayaran p JOIN anggota a ON a.id = p.anggota_id"
                + " UNION ALL"
                + " SELECT tanggal, 0, CONCAT(kategori, IF(keterangan IS NULL OR keterangan = '', '', CONCAT(' - ', keterangan))), jumlah"
                + "   FROM pengeluaran"
                + ") t"
                + (filter ? " WHERE DATE_FORMAT(tanggal, '%Y-%m') = ?" : "")
                + " ORDER BY tanggal DESC";
        try (Connection c = Koneksi.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            if (filter) {
                ps.setString(1, bulan);
            }
            List<Transaksi> hasil = new ArrayList<>();
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) {
                    hasil.add(new Transaksi(rs.getDate("tanggal").toLocalDate(),
                            rs.getInt("masuk") == 1, rs.getString("ket"), rs.getInt("jumlah")));
                }
            }
            return hasil;
        }
    }

    private long total(String sql, String bulan) throws SQLException {
        try (Connection c = Koneksi.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            if (adaFilter(bulan)) {
                ps.setString(1, bulan);
            }
            try (ResultSet rs = ps.executeQuery()) {
                rs.next();
                return rs.getLong(1);
            }
        }
    }

    private static boolean adaFilter(String bulan) {
        return bulan != null && !bulan.isEmpty();
    }
}
