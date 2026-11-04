package com.iurin.model;

import java.time.LocalDate;

public class Pembayaran {

    private int id;
    private int anggotaId;
    private String namaAnggota;
    /** Minggu iuran, format ISO "YYYY-Www". */
    private String minggu;
    private int jumlah;
    private LocalDate tanggalBayar;
    private String keterangan;

    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public int getAnggotaId() { return anggotaId; }
    public void setAnggotaId(int anggotaId) { this.anggotaId = anggotaId; }

    public String getNamaAnggota() { return namaAnggota; }
    public void setNamaAnggota(String namaAnggota) { this.namaAnggota = namaAnggota; }

    public String getMinggu() { return minggu; }
    public void setMinggu(String minggu) { this.minggu = minggu; }

    public int getJumlah() { return jumlah; }
    public void setJumlah(int jumlah) { this.jumlah = jumlah; }

    public LocalDate getTanggalBayar() { return tanggalBayar; }
    public void setTanggalBayar(LocalDate tanggalBayar) { this.tanggalBayar = tanggalBayar; }

    public String getKeterangan() { return keterangan; }
    public void setKeterangan(String keterangan) { this.keterangan = keterangan; }
}
