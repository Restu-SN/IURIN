package com.iurin.model;

import com.iurin.util.Koneksi;
import com.iurin.util.PasswordUtil;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

public class AdminDao {

    /** Mengembalikan admin jika username & password cocok, selain itu null. */
    public Admin login(String username, String password) throws SQLException {
        String sql = "SELECT id, username, nama, password_hash FROM admin WHERE username = ?";
        try (Connection c = Koneksi.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setString(1, username);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next() && PasswordUtil.verify(password, rs.getString("password_hash"))) {
                    return new Admin(rs.getInt("id"), rs.getString("username"), rs.getString("nama"));
                }
            }
        }
        return null;
    }

    public void gantiPassword(int adminId, String passwordBaru) throws SQLException {
        String sql = "UPDATE admin SET password_hash = ? WHERE id = ?";
        try (Connection c = Koneksi.getConnection(); PreparedStatement ps = c.prepareStatement(sql)) {
            ps.setString(1, PasswordUtil.hash(passwordBaru));
            ps.setInt(2, adminId);
            ps.executeUpdate();
        }
    }
}
