package com.iurin.model;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;

@Entity
@Table(name = "anggota")
public class Anggota {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private int id;

    @Column(name = "nama", nullable = false, unique = true, length = 100)
    private String nama;

    @Column(name = "nominal_iuran", nullable = false)
    private int nominalIuran;

    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public String getNama() { return nama; }
    public void setNama(String nama) { this.nama = nama; }

    public int getNominalIuran() { return nominalIuran; }
    public void setNominalIuran(int nominalIuran) { this.nominalIuran = nominalIuran; }

    @Override
    public String toString() {
        return nama;
    }
}
