package com.iurin.controller;

import com.iurin.model.PembayaranDao;
import com.iurin.model.StatusIuran;
import com.iurin.util.Format;
import java.io.IOException;
import org.hibernate.HibernateException;
import java.util.List;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/** Tabel status iuran (lunas / belum bayar / menunggak) per minggu. */
@WebServlet("/status")
public class StatusServlet extends BaseServlet {

    private final PembayaranDao pembayaranDao = new PembayaranDao();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        String minggu = param(req, "minggu");
        if (!Format.mingguValid(minggu)) {
            minggu = Format.mingguIni();
        }
        try {
            List<StatusIuran> daftar = pembayaranDao.statusMinggu(minggu);
            long lunas = daftar.stream().filter(s -> s.getStatus() == StatusIuran.Status.LUNAS).count();
            req.setAttribute("minggu", minggu);
            req.setAttribute("namaMinggu", Format.namaMinggu(minggu));
            req.setAttribute("daftar", daftar);
            req.setAttribute("jumlahLunas", lunas);
            req.setAttribute("jumlahBelum", daftar.size() - lunas);
            tampil(req, resp, "status", "status");
        } catch (HibernateException e) {
            throw new ServletException(e);
        }
    }
}
