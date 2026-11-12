package com.iurin.controller;

import com.iurin.model.Anggota;
import com.iurin.model.AnggotaDao;
import com.iurin.model.PembayaranDao;
import com.iurin.model.TalanganDao;
import com.iurin.util.Format;
import com.iurin.util.ValidasiException;
import java.io.IOException;
import java.time.LocalDate;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.hibernate.HibernateException;

/** Tambah, lihat, cari, edit, hapus anggota, serta catatan talangan per anggota. */
@WebServlet("/anggota")
public class AnggotaServlet extends BaseServlet {

    private final AnggotaDao anggotaDao = new AnggotaDao();
    private final PembayaranDao pembayaranDao = new PembayaranDao();
    private final TalanganDao talanganDao = new TalanganDao();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        try {
            String aksi = param(req, "aksi");
            if (aksi.equals("tambah") || aksi.equals("edit")) {
                Anggota a = aksi.equals("edit") ? anggotaDao.cariId(angka(req, "id")) : null;
                if (aksi.equals("edit") && a == null) {
                    flash(req, "Anggota tidak ditemukan.", "warning");
                    redirect(req, resp, "/anggota");
                    return;
                }
                tampilForm(req, resp, a);
                return;
            }

            String q = param(req, "q");
            req.setAttribute("q", q);
            req.setAttribute("daftar", anggotaDao.cari(q));
            req.setAttribute("rekap", pembayaranDao.rekapAnggota(Format.hariIni().toString().substring(0, 7)));
            req.setAttribute("talangan", talanganDao.sisa(null));
            tampil(req, resp, "anggota-list", "anggota");
        } catch (HibernateException e) {
            throw new ServletException(e);
        }
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        try {
            switch (param(req, "aksi")) {
                case "hapus":
                    anggotaDao.hapus(angka(req, "id"));
                    flash(req, "Anggota beserta riwayat mainnya telah dihapus.");
                    redirect(req, resp, "/anggota");
                    return;
                case "tambahTalangan":
                    tambahTalangan(req, resp);
                    return;
                case "hapusTalangan":
                    int anggotaId = talanganDao.hapus(angka(req, "talanganId"));
                    flash(req, "Catatan talangan dihapus.");
                    redirect(req, resp, anggotaId > 0 ? "/anggota?aksi=edit&id=" + anggotaId : "/anggota");
                    return;
                default:
                    simpan(req, resp);
            }
        } catch (HibernateException e) {
            throw new ServletException(e);
        }
    }

    private void simpan(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        Anggota a = new Anggota();
        a.setId(angka(req, "id"));
        a.setNama(param(req, "nama"));
        a.setNominalIuran(angka(req, "nominalIuran"));
        try {
            if (a.getNama().isEmpty()) {
                throw new ValidasiException("Nama wajib diisi.");
            }
            if (a.getId() == 0) {
                anggotaDao.tambah(a);
                flash(req, "Anggota " + a.getNama() + " berhasil ditambahkan.");
            } else {
                anggotaDao.ubah(a);
                flash(req, "Data " + a.getNama() + " berhasil diperbarui.");
            }
            redirect(req, resp, "/anggota");
        } catch (ValidasiException e) {
            req.setAttribute("error", e.getMessage());
            tampilForm(req, resp, a);
        }
    }

    /** Catat talangan baru untuk anggota ini (mis. bayarin sewa lapangan dulu). */
    private void tambahTalangan(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        int id = angka(req, "id");
        LocalDate tgl = tanggal(req, "tanggal");
        String ket = param(req, "keterangan");
        int jumlah = angka(req, "jumlah");
        try {
            talanganDao.tambah(id, tgl != null ? tgl : Format.hariIni(), jumlah,
                    ket.isEmpty() ? null : ket.substring(0, Math.min(255, ket.length())));
            flash(req, "Talangan " + Format.rupiah(jumlah) + " tersimpan.");
        } catch (ValidasiException e) {
            flash(req, e.getMessage(), "danger");
        }
        redirect(req, resp, "/anggota?aksi=edit&id=" + id);
    }

    private void tampilForm(HttpServletRequest req, HttpServletResponse resp, Anggota a)
            throws ServletException, IOException {
        req.setAttribute("anggota", a);
        if (a != null && a.getId() > 0) {
            req.setAttribute("daftarTalangan", talanganDao.daftar(a.getId()));
            req.setAttribute("rincianTalangan", talanganDao.rincian(null).get(a.getId()));
            req.setAttribute("hariIni", Format.hariIni());
        }
        tampil(req, resp, "anggota-form", "anggota");
    }
}
