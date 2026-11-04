package com.iurin.model;

public class Anggota {

    private int id;
    private String nama;
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
