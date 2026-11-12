package com.iurin.model;

import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;

/** Ringkasan satu hari main: siapa yang datang, uang masuk, pengeluaran, dan sisanya. */
public class Sesi {

    private final LocalDate tanggal;
    private final List<String> hadir = new ArrayList<>();
    private final List<String> pengeluaran = new ArrayList<>();
    private int guest;
    private int masuk;
    private int keluar;

    public Sesi(LocalDate tanggal) {
        this.tanggal = tanggal;
    }

    void tambahHadir(String nama, int jumlah) {
        hadir.add(nama);
        masuk += jumlah;
    }

    void tambahGuest(int orang, int uang) {
        guest = orang;
        masuk += uang;
    }

    void tambahPengeluaran(String kategori, int jumlah) {
        pengeluaran.add(kategori);
        keluar += jumlah;
    }

    public LocalDate getTanggal() { return tanggal; }
    public List<String> getHadir() { return hadir; }
    public String getHadirTeks() { return String.join(", ", hadir); }
    public List<String> getPengeluaran() { return pengeluaran; }
    public String getPengeluaranTeks() { return String.join(", ", pengeluaran); }
    public int getGuest() { return guest; }
    public int getJumlahOrang() { return hadir.size() + guest; }
    public int getMasuk() { return masuk; }
    public int getKeluar() { return keluar; }
    public int getSisa() { return masuk - keluar; }
}
