package com.iurin.controller;

import com.iurin.model.KasDao;
import com.iurin.model.Pengeluaran;
import java.io.IOException;
import java.sql.SQLException;
import java.time.LocalDate;
import java.time.format.DateTimeParseException;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/** Rekap kas klub: pemasukan, pengeluaran, saldo, dan pencatatan pengeluaran. */
@WebServlet("/kas")
public class KasServlet extends BaseServlet {

    private final KasDao kasDao = new KasDao();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        String bulan = param(req, "bulan");
        if (!bulan.matches("\\d{4}-\\d{2}")) {
            bulan = "";
        }
        try {
            long masuk = kasDao.totalPemasukan(bulan);
            long keluar = kasDao.totalPengeluaran(bulan);
            req.setAttribute("bulan", bulan);
            req.setAttribute("pemasukan", masuk);
            req.setAttribute("pengeluaran", keluar);
            req.setAttribute("saldo", masuk - keluar);
            req.setAttribute("saldoTotal", kasDao.totalPemasukan("") - kasDao.totalPengeluaran(""));
            req.setAttribute("transaksi", kasDao.transaksi(bulan));
            req.setAttribute("daftarPengeluaran", kasDao.daftarPengeluaran(bulan));
            req.setAttribute("hariIni", LocalDate.now());
            tampil(req, resp, "kas", "kas");
        } catch (SQLException e) {
            throw new ServletException(e);
        }
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        String kembali = "/kas?bulan=" + param(req, "filterBulan");
        try {
            if (param(req, "aksi").equals("hapus")) {
                kasDao.hapusPengeluaran(angka(req, "id"));
                flash(req, "Pengeluaran dihapus.", "success");
            } else {
                Pengeluaran p = new Pengeluaran();
                p.setKategori(param(req, "kategori"));
                p.setKeterangan(param(req, "keterangan"));
                p.setJumlah(angka(req, "jumlah"));
                try {
                    p.setTanggal(LocalDate.parse(param(req, "tanggal")));
                } catch (DateTimeParseException e) {
                    p.setTanggal(null);
                }
                if (p.getTanggal() == null || p.getKategori().isEmpty() || p.getJumlah() <= 0) {
                    flash(req, "Lengkapi tanggal, kategori, dan nominal pengeluaran.", "danger");
                } else {
                    kasDao.tambahPengeluaran(p);
                    flash(req, "Pengeluaran " + p.getKategori() + " tersimpan.", "success");
                }
            }
            redirect(req, resp, kembali);
        } catch (SQLException e) {
            throw new ServletException(e);
        }
    }
}
