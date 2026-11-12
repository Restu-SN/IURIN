package com.iurin.model;

import com.iurin.util.HibernateUtil;
import java.time.LocalDate;
import java.time.YearMonth;
import java.util.ArrayList;
import java.util.Comparator;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.function.Consumer;
import org.hibernate.Session;

/**
 * Kas klub: pengeluaran hari main, kas minggu lalu, guest, dan rekap
 * (pemasukan dari yang main & guest, pengeluaran, saldo).
 */
public class KasDao {

    /** Isian satu jenis pengeluaran dari form. */
    public static final class Isian {
        final int jumlah;
        final String keterangan;

        public Isian(int jumlah, String keterangan) {
            this.jumlah = jumlah;
            this.keterangan = keterangan;
        }
    }

    // ------------------------------------------------------------ kas_hari

    public KasHari kasHari(LocalDate tanggal) {
        return HibernateUtil.baca(s -> cariKasHari(s, tanggal));
    }

    public int kasLalu(LocalDate tanggal) {
        KasHari k = kasHari(tanggal);
        return k == null ? 0 : k.getKasLalu();
    }

    /** Simpan kas minggu lalu untuk tanggal itu; 0 = kosongkan. */
    public void simpanKasLalu(LocalDate tanggal, int jumlah) {
        ubahKasHari(tanggal, k -> k.setKasLalu(Math.max(0, jumlah)));
    }

    /** Simpan guest: jumlah orang x tarif. 0 orang = tidak ada guest. */
    public void simpanGuest(LocalDate tanggal, int orang, int tarif) {
        ubahKasHari(tanggal, k -> {
            k.setGuest(Math.max(0, orang));
            k.setTarifGuest(Math.max(0, tarif));
        });
    }

    /** Catatan guest dalam sebulan (kosong = semua waktu). */
    public List<KasHari> daftarGuest(String bulan) {
        boolean filter = bulan != null && !bulan.isEmpty();
        return HibernateUtil.baca(s -> {
            var q = s.createQuery("from KasHari k where k.guest > 0"
                    + (filter ? " and k.tanggal between :awal and :akhir" : "") + " order by k.tanggal desc", KasHari.class);
            setBulan(q, bulan);
            return q.getResultList();
        });
    }

    /** Ubah catatan tanggal itu (dibuat kalau belum ada, dihapus kalau jadi kosong). */
    private void ubahKasHari(LocalDate tanggal, Consumer<KasHari> ubah) {
        HibernateUtil.tulis(s -> {
            KasHari k = cariKasHari(s, tanggal);
            boolean baru = k == null;
            if (baru) {
                k = new KasHari();
                k.setTanggal(tanggal);
            }
            ubah.accept(k);
            if (k.isKosong()) {
                if (!baru) {
                    s.remove(k);
                }
            } else if (baru) {
                s.persist(k);
            }
        });
    }

    private static KasHari cariKasHari(Session s, LocalDate tanggal) {
        return s.createQuery("from KasHari where tanggal = :tgl", KasHari.class)
                .setParameter("tgl", tanggal).uniqueResult();
    }

    // ------------------------------------------------------------ pengeluaran

    /** Pengeluaran bulan "YYYY-MM" (kosong = semua), terbaru di atas. */
    public List<Pengeluaran> daftarPengeluaran(String bulan) {
        boolean filter = bulan != null && !bulan.isEmpty();
        return HibernateUtil.baca(s -> {
            var q = s.createQuery("from Pengeluaran p"
                    + (filter ? " where p.tanggal between :awal and :akhir" : "")
                    + " order by p.tanggal desc, p.id desc", Pengeluaran.class);
            setBulan(q, bulan);
            return q.getResultList();
        });
    }

    /** Pengeluaran pada satu hari main, urut sesuai waktu input. */
    public List<Pengeluaran> daftarPengeluaranTanggal(LocalDate tanggal) {
        return HibernateUtil.baca(s -> s
                .createQuery("from Pengeluaran p where p.tanggal = :tgl order by p.id", Pengeluaran.class)
                .setParameter("tgl", tanggal).getResultList());
    }

    /**
     * Menyimpan pengeluaran satu hari main sekaligus: satu nominal per jenis.
     * Nominal diisi = tambah/ubah, dikosongkan (0) = hapus.
     */
    public void simpanPengeluaranHari(LocalDate tanggal, Map<String, Isian> isian) {
        HibernateUtil.tulis(s -> {
            Map<String, List<Pengeluaran>> lama = new HashMap<>();
            for (Pengeluaran p : s.createQuery("from Pengeluaran p where p.tanggal = :tgl order by p.id", Pengeluaran.class)
                    .setParameter("tgl", tanggal).getResultList()) {
                lama.computeIfAbsent(Pengeluaran.kunci(p.getKategori()), k -> new ArrayList<>()).add(p);
            }
            isian.forEach((kategori, data) -> {
                List<Pengeluaran> ada = lama.getOrDefault(Pengeluaran.kunci(kategori), List.of());
                // Kalau dulu tercatat lebih dari sekali, disatukan jadi satu baris
                for (int i = 1; i < ada.size(); i++) {
                    s.remove(ada.get(i));
                }
                Pengeluaran p = ada.isEmpty() ? null : ada.get(0);
                if (data.jumlah <= 0) {
                    if (p != null) {
                        s.remove(p);
                    }
                    return;
                }
                boolean baru = p == null;
                if (baru) {
                    p = new Pengeluaran();
                    p.setTanggal(tanggal);
                }
                p.setKategori(kategori);
                p.setJumlah(data.jumlah);
                p.setKeterangan(data.keterangan);
                if (baru) {
                    s.persist(p);   // disimpan setelah kolom wajibnya terisi
                }
            });
        });
    }

    /**
     * Pisahkan pengeluaran yang diambil dari kas minggu lalu (beli air, kok)
     * dari yang dibayar pakai uang hari itu (parkir, lainnya).
     * Indeks 0 = dari kas minggu lalu, indeks 1 = dari uang hari ini.
     */
    public static List<List<Pengeluaran>> pisahKasLalu(List<Pengeluaran> pengeluaran) {
        List<Pengeluaran> dariKas = new ArrayList<>();
        List<Pengeluaran> hariIni = new ArrayList<>();
        for (Pengeluaran p : pengeluaran) {
            (Pengeluaran.dariKasLalu(p.getKategori()) ? dariKas : hariIni).add(p);
        }
        return List.of(dariKas, hariIni);
    }

    public static int jumlah(List<Pengeluaran> daftar) {
        return daftar.stream().mapToInt(Pengeluaran::getJumlah).sum();
    }

    // ------------------------------------------------------------ rekap

    /** Pemasukan = iuran tunai yang main + guest; bulan kosong = semua waktu. */
    public long totalPemasukan(String bulan) {
        boolean filter = bulan != null && !bulan.isEmpty();
        return HibernateUtil.baca(s -> {
            var q1 = s.createQuery("select coalesce(sum(p.jumlah), 0) from Pembayaran p"
                    + (filter ? " where p.tanggalBayar between :awal and :akhir" : ""), Long.class);
            var q2 = s.createQuery("select coalesce(sum(k.guest * k.tarifGuest), 0) from KasHari k"
                    + (filter ? " where k.tanggal between :awal and :akhir" : ""), Long.class);
            setBulan(q1, bulan);
            setBulan(q2, bulan);
            return q1.getSingleResult() + q2.getSingleResult();
        });
    }

    public long totalPengeluaran(String bulan) {
        boolean filter = bulan != null && !bulan.isEmpty();
        return HibernateUtil.baca(s -> {
            var q = s.createQuery("select coalesce(sum(p.jumlah), 0) from Pengeluaran p"
                    + (filter ? " where p.tanggal between :awal and :akhir" : ""), Long.class);
            setBulan(q, bulan);
            return q.getSingleResult();
        });
    }

    /** Gabungan pemasukan dan pengeluaran, terbaru di atas. */
    public List<Transaksi> transaksi(String bulan) {
        List<Transaksi> hasil = new ArrayList<>();
        for (Pembayaran p : new PembayaranDao().daftar(bulan)) {
            if (p.getJumlah() <= 0) {
                continue; // seluruh iurannya memotong talangan, tidak ada uang masuk
            }
            hasil.add(new Transaksi(p.getTanggalBayar(), true, "Main " + p.getAnggota().getNama()
                    + (p.getPotongTalangan() > 0 ? " (sebagian potong talangan)" : ""), p.getJumlah()));
        }
        for (KasHari k : daftarGuest(bulan)) {
            hasil.add(new Transaksi(k.getTanggal(), true, "Guest " + k.getGuest() + " orang (" + k.getGuest()
                    + " x " + com.iurin.util.Format.angka(k.getTarifGuest()) + ")", k.getMasukGuest()));
        }
        for (Pengeluaran p : daftarPengeluaran(bulan)) {
            String ket = p.getKeterangan() == null || p.getKeterangan().isEmpty() ? "" : " - " + p.getKeterangan();
            hasil.add(new Transaksi(p.getTanggal(), false, p.getKategori() + ket, p.getJumlah()));
        }
        hasil.sort(Comparator.comparing(Transaksi::getTanggal).reversed());
        return hasil;
    }

    /** Total kas di akhir satu hari main: kas minggu lalu + uang masuk - pengeluaran. */
    public long totalKasHari(LocalDate tanggal) {
        return HibernateUtil.baca(s -> {
            long masuk = s.createQuery("select coalesce(sum(p.jumlah), 0) from Pembayaran p where p.tanggalBayar = :tgl", Long.class)
                    .setParameter("tgl", tanggal).getSingleResult();
            long keluar = s.createQuery("select coalesce(sum(p.jumlah), 0) from Pengeluaran p where p.tanggal = :tgl", Long.class)
                    .setParameter("tgl", tanggal).getSingleResult();
            KasHari k = cariKasHari(s, tanggal);
            return (k == null ? 0 : k.getKasLalu() + k.getMasukGuest()) + masuk - keluar;
        });
    }

    /** Hari main terakhir sebelum tanggal ini; null kalau belum ada. */
    public LocalDate hariMainSebelum(LocalDate tanggal) {
        return HibernateUtil.baca(s -> {
            LocalDate terakhir = null;
            for (String hql : List.of(
                    "select max(p.tanggalBayar) from Pembayaran p where p.tanggalBayar < :tgl",
                    "select max(p.tanggal) from Pengeluaran p where p.tanggal < :tgl",
                    "select max(k.tanggal) from KasHari k where k.tanggal < :tgl")) {
                LocalDate t = s.createQuery(hql, LocalDate.class).setParameter("tgl", tanggal).getSingleResult();
                if (t != null && (terakhir == null || t.isAfter(terakhir))) {
                    terakhir = t;
                }
            }
            return terakhir;
        });
    }

    /** Hapus semua catatan satu tanggal main: yang main, pengeluaran, guest & kas minggu lalu. */
    public void hapusHari(LocalDate tanggal) {
        HibernateUtil.tulis(s -> {
            s.createMutationQuery("delete from Pembayaran p where p.tanggalBayar = :tgl").setParameter("tgl", tanggal).executeUpdate();
            s.createMutationQuery("delete from Pengeluaran p where p.tanggal = :tgl").setParameter("tgl", tanggal).executeUpdate();
            s.createMutationQuery("delete from KasHari k where k.tanggal = :tgl").setParameter("tgl", tanggal).executeUpdate();
        });
    }

    private static void setBulan(org.hibernate.query.Query<?> q, String bulan) {
        if (bulan != null && !bulan.isEmpty()) {
            YearMonth ym = YearMonth.parse(bulan);
            q.setParameter("awal", ym.atDay(1)).setParameter("akhir", ym.atEndOfMonth());
        }
    }
}
