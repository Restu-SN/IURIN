package com.iurin.controller;

import com.iurin.model.AnggotaDao;
import com.iurin.model.KasDao;
import com.iurin.model.KasHari;
import com.iurin.model.Pembayaran;
import com.iurin.model.PembayaranDao;
import com.iurin.model.Pengeluaran;
import com.iurin.model.TalanganDao;
import com.iurin.util.Format;
import java.io.IOException;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.Set;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.hibernate.HibernateException;

/**
 * Catat Main: centang siapa yang datang pada tanggal main (otomatis bayar sesuai
 * iurannya, dipotong talangan kalau ada), guest, kas minggu lalu, dan pengeluaran hari itu.
 */
@WebServlet("/pembayaran")
public class PembayaranServlet extends BaseServlet {

    private static final Map<String, String> IKON = Map.of(
            "Beli Air", "bi-droplet", "Uang Parkir", "bi-p-square", "Pembelian Kok", "bi-bag", "Lainnya", "bi-three-dots");

    private final AnggotaDao anggotaDao = new AnggotaDao();
    private final PembayaranDao pembayaranDao = new PembayaranDao();
    private final KasDao kasDao = new KasDao();
    private final TalanganDao talanganDao = new TalanganDao();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        try {
            LocalDate tanggal = tanggal(req, "tanggal");
            if (tanggal == null) {
                tanggal = Format.hariIni();
            }
            List<Pembayaran> hadir = pembayaranDao.daftarTanggal(tanggal);
            List<Pengeluaran> pengeluaran = kasDao.daftarPengeluaranTanggal(tanggal);
            KasHari kasHari = kasDao.kasHari(tanggal);
            int guest = kasHari == null ? 0 : kasHari.getGuest();
            int tarifGuest = kasHari != null && guest > 0 ? kasHari.getTarifGuest() : 15000;
            int kasLalu = kasHari == null ? 0 : kasHari.getKasLalu();
            int masuk = hadir.stream().mapToInt(Pembayaran::getJumlah).sum() + guest * tarifGuest;
            int keluar = KasDao.jumlah(pengeluaran);
            List<Pengeluaran> dariKas = KasDao.pisahKasLalu(pengeluaran).get(0);
            int keluarKas = KasDao.jumlah(dariKas);

            Set<Integer> hadirIds = new HashSet<>();
            hadir.forEach(p -> hadirIds.add(p.getAnggota().getId()));

            LocalDate sebelum = kasDao.hariMainSebelum(tanggal);

            req.setAttribute("tanggal", tanggal);
            req.setAttribute("daftarAnggota", anggotaDao.semua());
            req.setAttribute("hadir", hadir);
            req.setAttribute("hadirIds", hadirIds);
            req.setAttribute("pengeluaran", pengeluaran);
            req.setAttribute("masuk", masuk);
            req.setAttribute("keluar", keluar);
            req.setAttribute("sisaHari", masuk - (keluar - keluarKas));
            req.setAttribute("guest", guest);
            req.setAttribute("tarifGuest", tarifGuest);
            req.setAttribute("kasLalu", kasLalu);
            req.setAttribute("dariKas", dariKas);
            req.setAttribute("keluarKas", keluarKas);
            req.setAttribute("totalKas", kasLalu + masuk - keluar);
            req.setAttribute("kasSebelumTanggal", sebelum);
            req.setAttribute("kasSebelumTotal", sebelum == null ? 0 : kasDao.totalKasHari(sebelum));
            req.setAttribute("sisaTalangan", talanganDao.sisa(null));
            req.setAttribute("barisKasLalu", baris(pengeluaran, true));
            req.setAttribute("barisHariIni", baris(pengeluaran, false));
            tampil(req, resp, "pembayaran", "pembayaran");
        } catch (HibernateException e) {
            throw new ServletException(e);
        }
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        LocalDate tanggal = tanggal(req, "tanggal");
        if (tanggal == null) {
            flash(req, "Tanggal main tidak valid.", "danger");
            redirect(req, resp, "/pembayaran");
            return;
        }
        try {
            switch (param(req, "aksi")) {
                case "pengeluaran":
                    // Kotak Pengeluaran: hanya yang dibayar pakai uang hari ini (parkir, lainnya)
                    kasDao.simpanPengeluaranHari(tanggal, isian(req, false));
                    flash(req, "Pengeluaran " + Format.namaTanggal(tanggal) + " tersimpan.");
                    break;
                case "kasLalu":
                    // Kotak Kas minggu lalu: nominal bawaan + beli air & kok yang diambil dari situ
                    kasDao.simpanKasLalu(tanggal, Math.max(0, angka(req, "kasLalu")));
                    kasDao.simpanPengeluaranHari(tanggal, isian(req, true));
                    flash(req, "Kas minggu lalu " + Format.namaTanggal(tanggal) + " tersimpan.");
                    break;
                default:
                    simpanHadir(req, tanggal);
            }
        } catch (HibernateException e) {
            throw new ServletException(e);
        }
        redirect(req, resp, "/pembayaran?tanggal=" + tanggal);
    }

    /** Simpan daftar yang datang (yang dicentang) dan jumlah guest. */
    private void simpanHadir(HttpServletRequest req, LocalDate tanggal) {
        List<Integer> ids = new ArrayList<>();
        String[] dicentang = req.getParameterValues("hadir");
        if (dicentang != null) {
            for (String s : dicentang) {
                int id = angka(s);
                if (id > 0) {
                    ids.add(id);
                }
            }
        }
        int[] hasil = pembayaranDao.catatHadir(tanggal, ids);

        // Guest: cukup jumlah orangnya, uangnya = orang x tarif
        KasHari lama = kasDao.kasHari(tanggal);
        int guestLama = lama == null ? 0 : lama.getGuest();
        int guest = Math.max(0, Math.min(99, angka(req, "guest")));
        kasDao.simpanGuest(tanggal, guest, angka(req, "tarifGuest"));

        List<String> pesan = new ArrayList<>();
        if (hasil[0] > 0) {
            pesan.add(hasil[0] + " orang ditambahkan");
        }
        if (hasil[1] > 0) {
            pesan.add(hasil[1] + " orang dihapus");
        }
        if (guest != guestLama) {
            pesan.add(guest > 0 ? "guest " + guest + " orang" : "guest dihapus");
        }
        flash(req, pesan.isEmpty() ? "Tidak ada perubahan."
                : "Catatan main " + Format.namaTanggal(tanggal) + ": " + String.join(", ", pesan) + ".");
    }

    /**
     * Isian nominal (jumlah_i) & catatan (keterangan_i) dari form untuk kategori
     * yang diambil dari kas minggu lalu (dariKasLalu = true) atau dari uang hari ini.
     */
    private Map<String, KasDao.Isian> isian(HttpServletRequest req, boolean dariKasLalu) {
        Map<String, KasDao.Isian> hasil = new LinkedHashMap<>();
        for (int i = 0; i < Pengeluaran.KATEGORI.size(); i++) {
            String kategori = Pengeluaran.KATEGORI.get(i);
            if (Pengeluaran.dariKasLalu(kategori) != dariKasLalu) {
                continue;
            }
            String ket = param(req, "keterangan_" + i);
            hasil.put(kategori, new KasDao.Isian(Math.max(0, angka(req, "jumlah_" + i)),
                    ket.isEmpty() ? null : ket.substring(0, Math.min(255, ket.length()))));
        }
        return hasil;
    }

    /** Baris isian pengeluaran untuk form, terisi nilai yang sudah tercatat. */
    private List<Map<String, Object>> baris(List<Pengeluaran> pengeluaran, boolean dariKasLalu) {
        Map<String, int[]> jumlah = new HashMap<>();
        Map<String, String> ket = new HashMap<>();
        for (Pengeluaran p : pengeluaran) {
            String k = Pengeluaran.kunci(p.getKategori());
            jumlah.computeIfAbsent(k, x -> new int[1])[0] += p.getJumlah();
            ket.putIfAbsent(k, p.getKeterangan());
        }
        List<Map<String, Object>> hasil = new ArrayList<>();
        for (int i = 0; i < Pengeluaran.KATEGORI.size(); i++) {
            String kategori = Pengeluaran.KATEGORI.get(i);
            if (Pengeluaran.dariKasLalu(kategori) != dariKasLalu) {
                continue;
            }
            String k = Pengeluaran.kunci(kategori);
            int j = jumlah.containsKey(k) ? jumlah.get(k)[0] : 0;
            Map<String, Object> b = new HashMap<>();
            b.put("i", i);
            b.put("kategori", kategori);
            b.put("ikon", IKON.getOrDefault(kategori, "bi-dot"));
            b.put("nilai", j > 0 ? Format.angka(j) : "");
            b.put("ket", ket.getOrDefault(k, ""));
            b.put("pakaiCatatan", kategori.equals("Lainnya"));
            hasil.add(b);
        }
        return hasil;
    }
}
