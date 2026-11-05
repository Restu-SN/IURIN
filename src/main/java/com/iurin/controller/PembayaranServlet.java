package com.iurin.controller;

import com.iurin.model.AnggotaDao;
import com.iurin.model.PembayaranDao;
import com.iurin.model.Pembayaran;
import com.iurin.util.Format;
import com.iurin.util.ValidasiException;
import java.io.IOException;
import org.hibernate.HibernateException;
import java.time.LocalDate;
import java.time.format.DateTimeParseException;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/** Mencatat pembayaran iuran mingguan dan menampilkan riwayatnya. */
@WebServlet("/pembayaran")
public class PembayaranServlet extends BaseServlet {

    private final AnggotaDao anggotaDao = new AnggotaDao();
    private final PembayaranDao pembayaranDao = new PembayaranDao();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        try {
            tampilForm(req, resp, null);
        } catch (HibernateException e) {
            throw new ServletException(e);
        }
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        try {
            if (param(req, "aksi").equals("hapus")) {
                pembayaranDao.hapus(angka(req, "id"));
                flash(req, "Pembayaran dihapus.", "success");
                redirect(req, resp, "/pembayaran?minggu=" + param(req, "minggu"));
                return;
            }
            Pembayaran p = new Pembayaran();
            p.setAnggotaId(angka(req, "anggotaId"));
            p.setMinggu(param(req, "minggu"));
            p.setJumlah(angka(req, "jumlah"));
            p.setKeterangan(param(req, "keterangan"));
            try {
                p.setTanggalBayar(LocalDate.parse(param(req, "tanggalBayar")));
                if (p.getAnggotaId() == 0 || !Format.mingguValid(p.getMinggu())) {
                    throw new ValidasiException("Pilih anggota dan minggu iuran.");
                }
                if (p.getJumlah() <= 0) {
                    throw new ValidasiException("Nominal pembayaran harus lebih dari 0.");
                }
                pembayaranDao.tambah(p);
                flash(req, "Pembayaran iuran " + Format.namaMinggu(p.getMinggu()) + " tersimpan.", "success");
                redirect(req, resp, "/pembayaran?minggu=" + p.getMinggu());
            } catch (ValidasiException | DateTimeParseException e) {
                req.setAttribute("error", e instanceof DateTimeParseException ? "Tanggal bayar tidak valid." : e.getMessage());
                tampilForm(req, resp, p);
            }
        } catch (HibernateException e) {
            throw new ServletException(e);
        }
    }

    private void tampilForm(HttpServletRequest req, HttpServletResponse resp, Pembayaran input)
            throws ServletException, IOException {
        String minggu = param(req, "minggu");
        if (!Format.mingguValid(minggu)) {
            minggu = Format.mingguIni();
        }
        req.setAttribute("minggu", minggu);
        req.setAttribute("namaMinggu", Format.namaMinggu(minggu));
        req.setAttribute("input", input);
        req.setAttribute("hariIni", LocalDate.now());
        req.setAttribute("daftarAnggota", anggotaDao.semua());
        req.setAttribute("riwayat", pembayaranDao.daftar(minggu));
        tampil(req, resp, "pembayaran", "pembayaran");
    }
}
