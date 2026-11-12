package com.iurin.controller;

import com.iurin.model.Pengguna;
import com.iurin.model.PenggunaDao;
import java.io.IOException;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.hibernate.HibernateException;

@WebServlet("/password")
public class PasswordServlet extends BaseServlet {

    private final PenggunaDao penggunaDao = new PenggunaDao();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        tampil(req, resp, "password", "");
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        Pengguna p = pengguna(req);
        String lama = req.getParameter("lama");
        String baru = req.getParameter("baru");
        try {
            if (penggunaDao.login(p.getUsername(), lama) == null) {
                req.setAttribute("error", "Password lama salah.");
            } else if (baru == null || baru.length() < 6 || !baru.equals(req.getParameter("ulang"))) {
                req.setAttribute("error", "Password baru minimal 6 karakter dan harus sama dengan konfirmasi.");
            } else {
                penggunaDao.gantiPassword(p.getId(), baru);
                flash(req, "Password berhasil diganti.");
                redirect(req, resp, "/anggota");
                return;
            }
            tampil(req, resp, "password", "");
        } catch (HibernateException e) {
            throw new ServletException(e);
        }
    }
}
