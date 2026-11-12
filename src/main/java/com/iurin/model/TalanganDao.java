package com.iurin.model;

import com.iurin.util.HibernateUtil;
import com.iurin.util.ValidasiException;
import java.time.LocalDate;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 * Talangan anggota: total yang ditalangin dikurangi yang sudah terpotong
 * dari iuran main (kolom pembayaran.potong_talangan) = sisa talangan.
 */
public class TalanganDao {

    /** Talangan milik satu anggota, terbaru di atas. */
    public List<Talangan> daftar(int anggotaId) {
        return HibernateUtil.baca(s -> s
                .createQuery("from Talangan t where t.anggota.id = :id order by t.tanggal desc, t.id desc", Talangan.class)
                .setParameter("id", anggotaId)
                .getResultList());
    }

    public void tambah(int anggotaId, LocalDate tanggal, int jumlah, String keterangan) throws ValidasiException {
        if (jumlah <= 0) {
            throw new ValidasiException("Nominal talangan harus lebih dari 0.");
        }
        Anggota anggota = HibernateUtil.baca(s -> s.get(Anggota.class, anggotaId));
        if (anggota == null) {
            throw new ValidasiException("Anggota tidak ditemukan.");
        }
        Talangan t = new Talangan();
        t.setAnggota(anggota);
        t.setTanggal(tanggal);
        t.setJumlah(jumlah);
        t.setKeterangan(keterangan);
        HibernateUtil.tulis(s -> s.persist(t));
    }

    /** Hapus satu catatan talangan; mengembalikan id anggotanya (0 kalau tidak ada). */
    public int hapus(int id) {
        int[] anggotaId = {0};
        HibernateUtil.tulis(s -> {
            Talangan t = s.get(Talangan.class, id);
            if (t != null) {
                anggotaId[0] = t.getAnggota().getId();
                s.remove(t);
            }
        });
        return anggotaId[0];
    }

    /** Rincian talangan per anggota sampai tanggal tertentu (null = sampai sekarang). */
    public Map<Integer, RincianTalangan> rincian(LocalDate sampai) {
        String syaratT = sampai != null ? " where t.tanggal <= :tgl" : "";
        String syaratP = sampai != null ? " and p.tanggalBayar <= :tgl" : "";
        return HibernateUtil.baca(s -> {
            var qT = s.createQuery("select t.anggota.id, sum(t.jumlah) from Talangan t" + syaratT
                    + " group by t.anggota.id", Object[].class);
            var qP = s.createQuery("select p.anggota.id, sum(p.potongTalangan) from Pembayaran p"
                    + " where p.potongTalangan > 0" + syaratP + " group by p.anggota.id", Object[].class);
            if (sampai != null) {
                qT.setParameter("tgl", sampai);
                qP.setParameter("tgl", sampai);
            }
            Map<Integer, RincianTalangan> hasil = new HashMap<>();
            for (Object[] r : qT.getResultList()) {
                hasil.computeIfAbsent((Integer) r[0], k -> new RincianTalangan()).setTotal(((Number) r[1]).longValue());
            }
            for (Object[] r : qP.getResultList()) {
                hasil.computeIfAbsent((Integer) r[0], k -> new RincianTalangan()).setTerpotong(((Number) r[1]).longValue());
            }
            return hasil;
        });
    }

    /** id anggota -> sisa talangan (hanya yang masih ada sisanya). */
    public Map<Integer, Integer> sisa(LocalDate sampai) {
        Map<Integer, Integer> hasil = new HashMap<>();
        rincian(sampai).forEach((id, r) -> {
            if (r.getSisa() > 0) {
                hasil.put(id, (int) r.getSisa());
            }
        });
        return hasil;
    }
}
