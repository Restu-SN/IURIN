package com.iurin.model;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;
import java.io.Serializable;

/** Akun login. Role admin boleh mengubah data, role anggota hanya melihat. */
@Entity
@Table(name = "pengguna")
public class Pengguna implements Serializable {

    public static final String ADMIN = "admin";
    public static final String ANGGOTA = "anggota";

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private int id;

    @Column(name = "username", nullable = false, unique = true, length = 50)
    private String username;

    @Column(name = "password_hash", nullable = false)
    private String passwordHash;

    @Column(name = "nama", nullable = false, length = 100)
    private String nama;

    @Column(name = "role", nullable = false, length = 20)
    private String role = ANGGOTA;

    public int getId() { return id; }

    public String getUsername() { return username; }
    public void setUsername(String username) { this.username = username; }

    public String getPasswordHash() { return passwordHash; }
    public void setPasswordHash(String passwordHash) { this.passwordHash = passwordHash; }

    public String getNama() { return nama; }
    public void setNama(String nama) { this.nama = nama; }

    public String getRole() { return role; }
    public void setRole(String role) { this.role = role; }

    public boolean isAdmin() { return ADMIN.equals(role); }
}
