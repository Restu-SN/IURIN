package com.iurin.model;

import com.iurin.util.HibernateUtil;
import java.time.LocalDate;
import java.time.YearMonth;
import java.util.ArrayList;
import java.util.Comparator;
import java.util.List;

/** Pengeluaran klub dan rekap kas (pemasukan dari iuran, pengeluaran, saldo). */
public class KasDao {

    public void tambahPengeluaran(Pengeluaran p) {
        HibernateUtil.tulis(s -> s.persist(p));
    }

    public void hapusPengeluaran(int id) {
        HibernateUtil.tulis(s -> {
            Pengeluaran p = s.get(Pengeluaran.class, id);
            if (p != null) {
                s.remove(p);
            }
        });
    }

    /** bulan "YYYY-MM"; null/kosong = semua bulan. */
    public List<Pengeluaran> daftarPengeluaran(String bulan) {
        return HibernateUtil.baca(s -> {
            var q = s.createQuery("from Pengeluaran p"
                    + (adaFilter(bulan) ? " where p.tanggal between :awal and :akhir" : "")
                    + " order by p.tanggal desc, p.id desc", Pengeluaran.class);
            if (adaFilter(bulan)) {
                q.setParameter("awal", awal(bulan)).setParameter("akhir", akhir(bulan));
            }
            return q.getResultList();
        });
    }

    /** Pemasukan dihitung dari tanggal bayar iuran; bulan kosong = semua waktu. */
    public long totalPemasukan(String bulan) {
        return total("select coalesce(sum(p.jumlah), 0) from Pembayaran p"
                + (adaFilter(bulan) ? " where p.tanggalBayar between :awal and :akhir" : ""), bulan);
    }

    public long totalPengeluaran(String bulan) {
        return total("select coalesce(sum(p.jumlah), 0) from Pengeluaran p"
                + (adaFilter(bulan) ? " where p.tanggal between :awal and :akhir" : ""), bulan);
    }

    /** Gabungan pemasukan dan pengeluaran, terbaru di atas. */
    public List<Transaksi> transaksi(String bulan) {
        List<Transaksi> hasil = new ArrayList<>();
        for (Pembayaran p : new PembayaranDao().daftar(null)) {
            if (!adaFilter(bulan) || YearMonth.from(p.getTanggalBayar()).toString().equals(bulan)) {
                hasil.add(new Transaksi(p.getTanggalBayar(), true,
                        "Iuran " + p.getNamaAnggota() + " (" + p.getMinggu() + ")", p.getJumlah()));
            }
        }
        for (Pengeluaran p : daftarPengeluaran(bulan)) {
            String ket = p.getKeterangan() == null || p.getKeterangan().isEmpty()
                    ? p.getKategori() : p.getKategori() + " - " + p.getKeterangan();
            hasil.add(new Transaksi(p.getTanggal(), false, ket, p.getJumlah()));
        }
        hasil.sort(Comparator.comparing(Transaksi::getTanggal).reversed());
        return hasil;
    }

    private long total(String hql, String bulan) {
        return HibernateUtil.baca(s -> {
            var q = s.createQuery(hql, Number.class);
            if (adaFilter(bulan)) {
                q.setParameter("awal", awal(bulan)).setParameter("akhir", akhir(bulan));
            }
            return q.getSingleResult().longValue();
        });
    }

    private static boolean adaFilter(String bulan) {
        return bulan != null && !bulan.isEmpty();
    }

    private static LocalDate awal(String bulan) {
        return YearMonth.parse(bulan).atDay(1);
    }

    private static LocalDate akhir(String bulan) {
        return YearMonth.parse(bulan).atEndOfMonth();
    }
}
