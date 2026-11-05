package com.iurin.model;

import com.iurin.util.HibernateUtil;
import com.iurin.util.ValidasiException;
import java.util.List;

public class AnggotaDao {

    /** Daftar anggota; keyword (boleh kosong) dicocokkan ke nama. */
    public List<Anggota> cari(String keyword) {
        String pola = "%" + (keyword == null ? "" : keyword.trim()) + "%";
        return HibernateUtil.baca(s -> s
                .createQuery("from Anggota where nama like :pola order by nama", Anggota.class)
                .setParameter("pola", pola)
                .getResultList());
    }

    public List<Anggota> semua() {
        return cari("");
    }

    public Anggota cariId(int id) {
        return HibernateUtil.baca(s -> s.get(Anggota.class, id));
    }

    public void tambah(Anggota a) throws ValidasiException {
        cekNama(a.getNama(), 0);
        HibernateUtil.tulis(s -> s.persist(a));
    }

    /** Hanya mengubah data anggota; riwayat pembayaran tidak disentuh. */
    public void ubah(Anggota a) throws ValidasiException {
        cekNama(a.getNama(), a.getId());
        HibernateUtil.tulis(s -> s.merge(a));
    }

    /** Riwayat iuran ikut terhapus lewat ON DELETE CASCADE di database. */
    public void hapus(int id) {
        HibernateUtil.tulis(s -> {
            Anggota a = s.get(Anggota.class, id);
            if (a != null) {
                s.remove(a);
            }
        });
    }

    private void cekNama(String nama, int kecualiId) throws ValidasiException {
        long jumlah = HibernateUtil.baca(s -> s
                .createQuery("select count(a) from Anggota a where a.nama = :nama and a.id <> :id", Long.class)
                .setParameter("nama", nama)
                .setParameter("id", kecualiId)
                .getSingleResult());
        if (jumlah > 0) {
            throw new ValidasiException("Anggota bernama " + nama + " sudah ada.");
        }
    }
}
