package com.iurin.model;

/** Status iuran satu anggota pada satu bulan. */
public class StatusIuran {

    public enum Status { LUNAS, BELUM_BAYAR, MENUNGGAK }

    private final Anggota anggota;
    private final Status status;
    private final Pembayaran pembayaran;

    public StatusIuran(Anggota anggota, Status status, Pembayaran pembayaran) {
        this.anggota = anggota;
        this.status = status;
        this.pembayaran = pembayaran;
    }

    public Anggota getAnggota() { return anggota; }
    public Status getStatus() { return status; }
    /** null jika belum bayar. */
    public Pembayaran getPembayaran() { return pembayaran; }

    public String getLabel() {
        switch (status) {
            case LUNAS: return "Lunas";
            case MENUNGGAK: return "Menunggak";
            default: return "Belum Bayar";
        }
    }
}
