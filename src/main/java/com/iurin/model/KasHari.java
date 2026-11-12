package com.iurin.model;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;
import java.time.LocalDate;

/**
 * Catatan tambahan satu tanggal main: kas minggu lalu (uang bawaan) dan
 * guest (orang di luar anggota, jumlah orang x tarif) yang ikut bayar.
 */
@Entity
@Table(name = "kas_hari")
public class KasHari {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private int id;

    @Column(name = "tanggal", nullable = false, unique = true)
    private LocalDate tanggal;

    @Column(name = "kas_minggu_lalu", nullable = false)
    private int kasLalu;

    @Column(name = "guest", nullable = false)
    private int guest;

    @Column(name = "tarif_guest", nullable = false)
    private int tarifGuest = 15000;

    public int getId() { return id; }

    public LocalDate getTanggal() { return tanggal; }
    public void setTanggal(LocalDate tanggal) { this.tanggal = tanggal; }

    public int getKasLalu() { return kasLalu; }
    public void setKasLalu(int kasLalu) { this.kasLalu = kasLalu; }

    public int getGuest() { return guest; }
    public void setGuest(int guest) { this.guest = guest; }

    public int getTarifGuest() { return tarifGuest; }
    public void setTarifGuest(int tarifGuest) { this.tarifGuest = tarifGuest; }

    /** Uang masuk dari guest: jumlah orang x tarif. */
    public int getMasukGuest() { return guest * tarifGuest; }

    /** Tidak ada isinya lagi, boleh dihapus. */
    public boolean isKosong() { return kasLalu <= 0 && guest <= 0; }
}
