package com.iurin.model;

import com.iurin.util.Koneksi;
import com.iurin.util.ValidasiException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

public class AnggotaDao {

    /** Daftar anggota; keyword (boleh kosong) dicocokkan ke nama. */
    public List<Anggota> cari(String keyword) throws SQLException {
        String sql = "SELECT * FROM anggota WHERE nama LIKE ? ORDER BY nama";
        try (Connection c = Koneksi.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setString(1, "%" + (keyword == null ? "" : keyword.trim()) + "%");
            return baca(ps);
        }
    }

    public List<Anggota> semua() throws SQLException {
        return cari("");
    }

    public Anggota cariId(int id) throws SQLException {
        String sql = "SELECT * FROM anggota WHERE id = ?";
        try (Connection c = Koneksi.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setInt(1, id);
            List<Anggota> hasil = baca(ps);
            return hasil.isEmpty() ? null : hasil.get(0);
        }
    }

    public void tambah(Anggota a) throws SQLException, ValidasiException {
        cekNama(a.getNama(), 0);
        String sql = "INSERT INTO anggota (nama, nominal_iuran) VALUES (?, ?)";
        try (Connection c = Koneksi.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setString(1, a.getNama());
            ps.setInt(2, a.getNominalIuran());
            ps.executeUpdate();
        }
    }

    /** Hanya mengubah data anggota; riwayat pembayaran tidak disentuh. */
    public void ubah(Anggota a) throws SQLException, ValidasiException {
        cekNama(a.getNama(), a.getId());
        String sql = "UPDATE anggota SET nama = ?, nominal_iuran = ? WHERE id = ?";
        try (Connection c = Koneksi.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setString(1, a.getNama());
            ps.setInt(2, a.getNominalIuran());
            ps.setInt(3, a.getId());
            ps.executeUpdate();
        }
    }

    /** Riwayat iuran ikut terhapus lewat ON DELETE CASCADE. */
    public void hapus(int id) throws SQLException {
        try (Connection c = Koneksi.getConnection();
                PreparedStatement ps = c.prepareStatement("DELETE FROM anggota WHERE id = ?")) {
            ps.setInt(1, id);
            ps.executeUpdate();
        }
    }

    private void cekNama(String nama, int kecualiId) throws SQLException, ValidasiException {
        String sql = "SELECT COUNT(*) FROM anggota WHERE nama = ? AND id <> ?";
        try (Connection c = Koneksi.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setString(1, nama);
            ps.setInt(2, kecualiId);
            try (ResultSet rs = ps.executeQuery()) {
                rs.next();
                if (rs.getInt(1) > 0) {
                    throw new ValidasiException("Anggota bernama " + nama + " sudah ada.");
                }
            }
        }
    }

    private List<Anggota> baca(PreparedStatement ps) throws SQLException {
        List<Anggota> hasil = new ArrayList<>();
        try (ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                hasil.add(dariRow(rs));
            }
        }
        return hasil;
    }

    static Anggota dariRow(ResultSet rs) throws SQLException {
        Anggota a = new Anggota();
        a.setId(rs.getInt("id"));
        a.setNama(rs.getString("nama"));
        a.setNominalIuran(rs.getInt("nominal_iuran"));
        return a;
    }
}
