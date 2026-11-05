package com.iurin.model;

import com.iurin.util.Format;
import com.iurin.util.HibernateUtil;
import com.iurin.util.ValidasiException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class PembayaranDao {

    /** Riwayat pembayaran; minggu null/kosong = semua minggu. */
    public List<Pembayaran> daftar(String minggu) {
        boolean filter = minggu != null && !minggu.isEmpty();
        String hql = "from Pembayaran p join fetch p.anggota"
                + (filter ? " where p.minggu = :minggu" : "")
                + " order by p.tanggalBayar desc, p.id desc";
        return HibernateUtil.baca(s -> {
            var q = s.createQuery(hql, Pembayaran.class);
            if (filter) {
                q.setParameter("minggu", minggu);
            }
            return q.getResultList();
        });
    }

    public void tambah(Pembayaran p) throws ValidasiException {
        long sudahAda = HibernateUtil.baca(s -> s
                .createQuery("select count(p) from Pembayaran p where p.anggota.id = :id and p.minggu = :minggu", Long.class)
                .setParameter("id", p.getAnggotaId())
                .setParameter("minggu", p.getMinggu())
                .getSingleResult());
        if (sudahAda > 0) {
            throw new ValidasiException("Anggota ini sudah membayar iuran " + Format.namaMinggu(p.getMinggu()) + ".");
        }
        HibernateUtil.tulis(s -> {
            p.setAnggota(s.getReference(Anggota.class, p.getAnggotaId()));
            s.persist(p);
        });
    }

    public void hapus(int id) {
        HibernateUtil.tulis(s -> {
            Pembayaran p = s.get(Pembayaran.class, id);
            if (p != null) {
                s.remove(p);
            }
        });
    }

    /**
     * Status iuran semua anggota pada minggu tersebut.
     * Lunas = ada pembayaran; Menunggak = belum bayar dan minggunya sudah lewat;
     * Belum Bayar = belum bayar untuk minggu berjalan / mendatang.
     */
    public List<StatusIuran> statusMinggu(String minggu) {
        List<Anggota> anggota = new AnggotaDao().semua();
        Map<Integer, Pembayaran> bayar = new HashMap<>();
        for (Pembayaran p : daftar(minggu)) {
            bayar.put(p.getAnggotaId(), p);
        }
        // Format YYYY-Www bisa dibandingkan langsung sebagai teks
        boolean sudahLewat = minggu.compareTo(Format.mingguIni()) < 0;
        List<StatusIuran> hasil = new ArrayList<>();
        for (Anggota a : anggota) {
            Pembayaran p = bayar.get(a.getId());
            StatusIuran.Status status = p != null ? StatusIuran.Status.LUNAS
                    : sudahLewat ? StatusIuran.Status.MENUNGGAK : StatusIuran.Status.BELUM_BAYAR;
            hasil.add(new StatusIuran(a, status, p));
        }
        return hasil;
    }
}
