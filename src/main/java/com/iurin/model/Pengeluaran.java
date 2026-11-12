package com.iurin.model;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;
import java.time.LocalDate;
import java.util.List;
import java.util.Locale;

@Entity
@Table(name = "pengeluaran")
public class Pengeluaran {

    /** Pilihan pengeluaran hari main. */
    public static final List<String> KATEGORI = List.of("Beli Air", "Uang Parkir", "Pembelian Kok", "Lainnya");

    /** Pengeluaran yang diambil dari kas minggu lalu. */
    public static final List<String> DARI_KAS_LALU = List.of("Beli Air", "Pembelian Kok");

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private int id;

    @Column(name = "tanggal", nullable = false)
    private LocalDate tanggal;

    @Column(name = "kategori", nullable = false, length = 50)
    private String kategori;

    @Column(name = "keterangan")
    private String keterangan;

    @Column(name = "jumlah", nullable = false)
    private int jumlah;

    /** Kunci pembanding kategori: tanpa beda huruf besar/kecil & spasi ("beli  air" = "Beli Air"). */
    public static String kunci(String kategori) {
        return kategori == null ? "" : kategori.trim().replaceAll("\\s+", " ").toLowerCase(Locale.ROOT);
    }

    /** Termasuk yang diambil dari kas minggu lalu? */
    public static boolean dariKasLalu(String kategori) {
        String k = kunci(kategori);
        return DARI_KAS_LALU.stream().anyMatch(d -> kunci(d).equals(k));
    }

    public int getId() { return id; }

    public LocalDate getTanggal() { return tanggal; }
    public void setTanggal(LocalDate tanggal) { this.tanggal = tanggal; }

    public String getKategori() { return kategori; }
    public void setKategori(String kategori) { this.kategori = kategori; }

    public String getKeterangan() { return keterangan; }
    public void setKeterangan(String keterangan) { this.keterangan = keterangan; }

    public int getJumlah() { return jumlah; }
    public void setJumlah(int jumlah) { this.jumlah = jumlah; }
}
