package com.iurin.util;

import java.io.IOException;
import java.io.InputStream;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.util.Properties;

/**
 * Membuka koneksi ke database MySQL. Pengaturan dibaca dari db.properties
 * (src/main/resources); jika tidak ada, dipakai default XAMPP.
 */
public final class Koneksi {

    private static final Properties CONFIG = new Properties();

    static {
        CONFIG.setProperty("db.url", "jdbc:mysql://localhost:3306/iurin?serverTimezone=Asia/Jakarta");
        CONFIG.setProperty("db.user", "root");
        CONFIG.setProperty("db.password", "");
        try (InputStream in = Koneksi.class.getResourceAsStream("/db.properties")) {
            if (in != null) {
                CONFIG.load(in);
            }
        } catch (IOException e) {
            throw new ExceptionInInitializerError(e);
        }
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
        } catch (ClassNotFoundException e) {
            throw new ExceptionInInitializerError("Driver MySQL tidak ditemukan: " + e.getMessage());
        }
    }

    private Koneksi() {
    }

    public static Connection getConnection() throws SQLException {
        return DriverManager.getConnection(
                CONFIG.getProperty("db.url"),
                CONFIG.getProperty("db.user"),
                CONFIG.getProperty("db.password"));
    }
}
