package com.iurin.model;

import java.time.LocalDate;

/** Satu baris di rekap kas: pemasukan (iuran) atau pengeluaran. */
public class Transaksi {

    private final LocalDate tanggal;
    private final boolean pemasukan;
    private final String keterangan;
    private final int jumlah;

    public Transaksi(LocalDate tanggal, boolean pemasukan, String keterangan, int jumlah) {
        this.tanggal = tanggal;
        this.pemasukan = pemasukan;
        this.keterangan = keterangan;
        this.jumlah = jumlah;
    }

    public LocalDate getTanggal() { return tanggal; }
    public boolean isPemasukan() { return pemasukan; }
    public String getJenis() { return pemasukan ? "Pemasukan" : "Pengeluaran"; }
    public String getKeterangan() { return keterangan; }
    public int getJumlah() { return jumlah; }
}
