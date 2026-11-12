package com.iurin.model;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.FetchType;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.Table;
import java.time.LocalDate;

/** Satu anggota yang datang main pada satu tanggal = satu pembayaran. */
@Entity
@Table(name = "pembayaran")
public class Pembayaran {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private int id;

    @ManyToOne(fetch = FetchType.EAGER, optional = false)
    @JoinColumn(name = "anggota_id", nullable = false)
    private Anggota anggota;

    /** Uang tunai yang masuk kas (iuran dikurangi potongan talangan). */
    @Column(name = "jumlah", nullable = false)
    private int jumlah;

    /** Bagian iuran yang dipakai untuk memotong talangan anggota ini. */
    @Column(name = "potong_talangan", nullable = false)
    private int potongTalangan;

    @Column(name = "tanggal_bayar", nullable = false)
    private LocalDate tanggalBayar;

    @Column(name = "keterangan")
    private String keterangan;

    public int getId() { return id; }

    public Anggota getAnggota() { return anggota; }
    public void setAnggota(Anggota anggota) { this.anggota = anggota; }

    public int getJumlah() { return jumlah; }
    public void setJumlah(int jumlah) { this.jumlah = jumlah; }

    public int getPotongTalangan() { return potongTalangan; }
    public void setPotongTalangan(int potongTalangan) { this.potongTalangan = potongTalangan; }

    /** Iuran sekali main sebelum dipotong talangan. */
    public int getIuran() { return jumlah + potongTalangan; }

    public LocalDate getTanggalBayar() { return tanggalBayar; }
    public void setTanggalBayar(LocalDate tanggalBayar) { this.tanggalBayar = tanggalBayar; }

    public String getKeterangan() { return keterangan; }
    public void setKeterangan(String keterangan) { this.keterangan = keterangan; }
}
