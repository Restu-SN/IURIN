package com.iurin.model;

import java.time.LocalDate;

public class Pengeluaran {

    private int id;
    private LocalDate tanggal;
    private String kategori;
    private String keterangan;
    private int jumlah;

    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public LocalDate getTanggal() { return tanggal; }
    public void setTanggal(LocalDate tanggal) { this.tanggal = tanggal; }

    public String getKategori() { return kategori; }
    public void setKategori(String kategori) { this.kategori = kategori; }

    public String getKeterangan() { return keterangan; }
    public void setKeterangan(String keterangan) { this.keterangan = keterangan; }

    public int getJumlah() { return jumlah; }
    public void setJumlah(int jumlah) { this.jumlah = jumlah; }
}
