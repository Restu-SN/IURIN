package com.iurin.model;

import com.iurin.util.HibernateUtil;
import java.time.LocalDate;
import java.time.YearMonth;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.LinkedHashSet;
import java.util.List;
import java.util.Map;
import java.util.TreeMap;

/**
 * Pembayaran dicatat per tanggal main: yang datang bayar sesuai iurannya,
 * yang tidak datang tidak bayar.
 */
public class PembayaranDao {

    private final TalanganDao talanganDao = new TalanganDao();
    private final KasDao kasDao = new KasDao();

    /** Pembayaran dalam bulan "YYYY-MM" (kosong = semua waktu), terbaru di atas. */
    public List<Pembayaran> daftar(String bulan) {
        boolean filter = bulan != null && !bulan.isEmpty();
        return HibernateUtil.baca(s -> {
            var q = s.createQuery("from Pembayaran p join fetch p.anggota a"
                    + (filter ? " where p.tanggalBayar between :awal and :akhir" : "")
                    + " order by p.tanggalBayar desc, a.nama asc", Pembayaran.class);
            if (filter) {
                YearMonth ym = YearMonth.parse(bulan);
                q.setParameter("awal", ym.atDay(1)).setParameter("akhir", ym.atEndOfMonth());
            }
            return q.getResultList();
        });
    }

    /** Anggota yang main pada tanggal tersebut. */
    public List<Pembayaran> daftarTanggal(LocalDate tanggal) {
        return HibernateUtil.baca(s -> s
                .createQuery("from Pembayaran p join fetch p.anggota a where p.tanggalBayar = :tgl order by a.nama", Pembayaran.class)
                .setParameter("tgl", tanggal)
                .getResultList());
    }

    /**
     * Menyamakan catatan tanggal itu dengan daftar yang dicentang:
     * yang baru dicentang ditambah (nominal = iuran anggota, dipotong talangan kalau ada),
     * yang dilepas dihapus. Mengembalikan {jumlah ditambah, jumlah dihapus}.
     */
    public int[] catatHadir(LocalDate tanggal, Collection<Integer> anggotaIds) {
        Map<Integer, Integer> sisaTalangan = talanganDao.sisa(null);
        int[] hasil = {0, 0};
        HibernateUtil.tulis(s -> {
            Map<Integer, Pembayaran> sudah = new HashMap<>();
            for (Pembayaran p : s.createQuery("from Pembayaran p where p.tanggalBayar = :tgl", Pembayaran.class)
                    .setParameter("tgl", tanggal).getResultList()) {
                sudah.put(p.getAnggota().getId(), p);
            }
            for (int id : new LinkedHashSet<>(anggotaIds)) {
                if (sudah.remove(id) != null) {
                    continue;
                }
                Anggota anggota = s.get(Anggota.class, id);
                if (anggota == null) {
                    continue;
                }
                // Kalau anggota ini punya talangan, iurannya memotong talangan dulu
                int iuran = anggota.getNominalIuran();
                int potong = Math.min(sisaTalangan.getOrDefault(id, 0), iuran);
                sisaTalangan.put(id, sisaTalangan.getOrDefault(id, 0) - potong);

                Pembayaran p = new Pembayaran();
                p.setAnggota(anggota);
                p.setTanggalBayar(tanggal);
                p.setJumlah(iuran - potong);
                p.setPotongTalangan(potong);
                s.persist(p);
                hasil[0]++;
            }
            for (Pembayaran p : sudah.values()) {
                s.remove(p);
                hasil[1]++;
            }
        });
        return hasil;
    }

    /**
     * Rekap per anggota: berapa kali main & total bayar pada bulan itu,
     * serta tanggal terakhir main (kapan pun). Kunci = id anggota.
     */
    public Map<Integer, RekapAnggota> rekapAnggota(String bulan) {
        YearMonth ym = YearMonth.parse(bulan);
        return HibernateUtil.baca(s -> {
            Map<Integer, RekapAnggota> hasil = new HashMap<>();
            for (Anggota a : s.createQuery("from Anggota", Anggota.class).getResultList()) {
                hasil.put(a.getId(), new RekapAnggota(a));
            }
            for (Object[] r : s.createQuery("select p.anggota.id, count(p), sum(p.jumlah) from Pembayaran p"
                            + " where p.tanggalBayar between :awal and :akhir group by p.anggota.id", Object[].class)
                    .setParameter("awal", ym.atDay(1)).setParameter("akhir", ym.atEndOfMonth()).getResultList()) {
                RekapAnggota rk = hasil.get((Integer) r[0]);
                if (rk != null) {
                    rk.setMain(((Number) r[1]).intValue());
                    rk.setTotal(((Number) r[2]).intValue());
                }
            }
            for (Object[] r : s.createQuery("select p.anggota.id, max(p.tanggalBayar) from Pembayaran p group by p.anggota.id",
                    Object[].class).getResultList()) {
                RekapAnggota rk = hasil.get((Integer) r[0]);
                if (rk != null) {
                    rk.setTerakhir((LocalDate) r[1]);
                }
            }
            return hasil;
        });
    }

    /** Ringkasan tiap hari main dalam sebulan (kosong = semua waktu), terbaru di atas. */
    public List<Sesi> sesiBulan(String bulan) {
        TreeMap<LocalDate, Sesi> sesi = new TreeMap<>();
        for (Pembayaran p : daftar(bulan)) {
            sesi.computeIfAbsent(p.getTanggalBayar(), Sesi::new).tambahHadir(p.getAnggota().getNama(), p.getJumlah());
        }
        for (KasHari k : kasDao.daftarGuest(bulan)) {
            sesi.computeIfAbsent(k.getTanggal(), Sesi::new).tambahGuest(k.getGuest(), k.getMasukGuest());
        }
        List<Pengeluaran> keluar = kasDao.daftarPengeluaran(bulan);
        for (int i = keluar.size() - 1; i >= 0; i--) {   // urut sesuai waktu input
            Pengeluaran p = keluar.get(i);
            sesi.computeIfAbsent(p.getTanggal(), Sesi::new).tambahPengeluaran(p.getKategori(), p.getJumlah());
        }
        return new ArrayList<>(sesi.descendingMap().values());
    }
}
