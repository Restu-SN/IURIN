package com.iurin.controller;

import com.iurin.model.AnggotaDao;
import com.iurin.model.PembayaranDao;
import com.iurin.model.Anggota;
import com.iurin.model.StatusIuran;
import com.iurin.util.Format;
import com.iurin.util.ValidasiException;
import java.io.IOException;
import org.hibernate.HibernateException;
import java.util.HashMap;
import java.util.Map;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/** Tambah, lihat, cari, edit, dan hapus anggota. */
@WebServlet("/anggota")
public class AnggotaServlet extends BaseServlet {

    private final AnggotaDao anggotaDao = new AnggotaDao();
    private final PembayaranDao pembayaranDao = new PembayaranDao();

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
                req.setAttribute("anggota", a);
                tampil(req, resp, "anggota-form", "anggota");
                return;
            }

            String q = param(req, "q");
            String minggu = Format.mingguIni();
            Map<Integer, StatusIuran> status = new HashMap<>();
            for (StatusIuran s : pembayaranDao.statusMinggu(minggu)) {
                status.put(s.getAnggota().getId(), s);
            }
            req.setAttribute("q", q);
            req.setAttribute("daftar", anggotaDao.cari(q));
            req.setAttribute("status", status);
            req.setAttribute("namaMinggu", Format.namaMinggu(minggu));
            tampil(req, resp, "anggota-list", "anggota");
        } catch (HibernateException e) {
            throw new ServletException(e);
        }
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        try {
            if (param(req, "aksi").equals("hapus")) {
                anggotaDao.hapus(angka(req, "id"));
                flash(req, "Anggota beserta riwayat iurannya telah dihapus.", "success");
                redirect(req, resp, "/anggota");
                return;
            }

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
                    flash(req, "Anggota " + a.getNama() + " berhasil ditambahkan.", "success");
                } else {
                    anggotaDao.ubah(a);
                    flash(req, "Data " + a.getNama() + " berhasil diperbarui.", "success");
                }
                redirect(req, resp, "/anggota");
            } catch (ValidasiException e) {
                req.setAttribute("error", e.getMessage());
                req.setAttribute("anggota", a);
                tampil(req, resp, "anggota-form", "anggota");
            }
        } catch (HibernateException e) {
            throw new ServletException(e);
        }
    }
}
