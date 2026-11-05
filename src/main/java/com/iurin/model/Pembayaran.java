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
import jakarta.persistence.Transient;
import java.time.LocalDate;

@Entity
@Table(name = "pembayaran")
public class Pembayaran {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private int id;

    @ManyToOne(fetch = FetchType.EAGER, optional = false)
    @JoinColumn(name = "anggota_id", nullable = false)
    private Anggota anggota;

    /** Format ISO "YYYY-Www", mis. 2026-W45. */
    @Column(name = "minggu", nullable = false, length = 8)
    private String minggu;

    @Column(name = "jumlah", nullable = false)
    private int jumlah;

    @Column(name = "tanggal_bayar", nullable = false)
    private LocalDate tanggalBayar;

    @Column(name = "keterangan")
    private String keterangan;

    /** Id anggota dari form, sebelum objek Anggota dimuat. */
    @Transient
    private int anggotaIdInput;

    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public Anggota getAnggota() { return anggota; }
    public void setAnggota(Anggota anggota) { this.anggota = anggota; }

    public int getAnggotaId() { return anggota != null ? anggota.getId() : anggotaIdInput; }
    public void setAnggotaId(int anggotaId) { this.anggotaIdInput = anggotaId; }

    public String getNamaAnggota() { return anggota != null ? anggota.getNama() : null; }

    public String getMinggu() { return minggu; }
    public void setMinggu(String minggu) { this.minggu = minggu; }

    public int getJumlah() { return jumlah; }
    public void setJumlah(int jumlah) { this.jumlah = jumlah; }

    public LocalDate getTanggalBayar() { return tanggalBayar; }
    public void setTanggalBayar(LocalDate tanggalBayar) { this.tanggalBayar = tanggalBayar; }

    public String getKeterangan() { return keterangan; }
    public void setKeterangan(String keterangan) { this.keterangan = keterangan; }
}
