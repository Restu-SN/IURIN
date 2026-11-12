package com.iurin.model;

import java.time.LocalDate;

/** Rekap satu anggota dalam sebulan: berapa kali main, total bayar, dan terakhir main. */
public class RekapAnggota {

    private final Anggota anggota;
    private int main;
    private int total;
    private LocalDate terakhir;

    public RekapAnggota(Anggota anggota) {
        this.anggota = anggota;
    }

    public Anggota getAnggota() { return anggota; }

    public int getMain() { return main; }
    public void setMain(int main) { this.main = main; }

    public int getTotal() { return total; }
    public void setTotal(int total) { this.total = total; }

    public LocalDate getTerakhir() { return terakhir; }
    public void setTerakhir(LocalDate terakhir) { this.terakhir = terakhir; }
}
