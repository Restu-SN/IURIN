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

/**
 * Uang klub yang ditalangin (dibayari dulu) oleh anggota, mis. sewa lapangan.
 * Dilunasi pelan-pelan: tiap anggota itu main, iurannya memotong talangan.
 */
@Entity
@Table(name = "talangan")
public class Talangan {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private int id;

    @ManyToOne(fetch = FetchType.EAGER, optional = false)
    @JoinColumn(name = "anggota_id", nullable = false)
    private Anggota anggota;

    @Column(name = "tanggal", nullable = false)
    private LocalDate tanggal;

    @Column(name = "jumlah", nullable = false)
    private int jumlah;

    @Column(name = "keterangan")
    private String keterangan;

    public int getId() { return id; }

    public Anggota getAnggota() { return anggota; }
    public void setAnggota(Anggota anggota) { this.anggota = anggota; }

    public LocalDate getTanggal() { return tanggal; }
    public void setTanggal(LocalDate tanggal) { this.tanggal = tanggal; }

    public int getJumlah() { return jumlah; }
    public void setJumlah(int jumlah) { this.jumlah = jumlah; }

    public String getKeterangan() { return keterangan; }
    public void setKeterangan(String keterangan) { this.keterangan = keterangan; }
}
