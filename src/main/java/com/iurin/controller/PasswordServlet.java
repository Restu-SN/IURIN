package com.iurin.controller;

import com.iurin.model.AdminDao;
import com.iurin.model.Admin;
import java.io.IOException;
import org.hibernate.HibernateException;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

@WebServlet("/password")
public class PasswordServlet extends BaseServlet {

    private final AdminDao adminDao = new AdminDao();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        tampil(req, resp, "password", "");
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        Admin admin = (Admin) req.getSession().getAttribute("admin");
        String lama = req.getParameter("lama");
        String baru = req.getParameter("baru");
        try {
            if (adminDao.login(admin.getUsername(), lama) == null) {
                req.setAttribute("error", "Password lama salah.");
            } else if (baru == null || baru.length() < 6 || !baru.equals(req.getParameter("ulang"))) {
                req.setAttribute("error", "Password baru minimal 6 karakter dan harus sama dengan konfirmasi.");
            } else {
                adminDao.gantiPassword(admin.getId(), baru);
                flash(req, "Password berhasil diganti.", "success");
                redirect(req, resp, "/anggota");
                return;
            }
            tampil(req, resp, "password", "");
        } catch (HibernateException e) {
            throw new ServletException(e);
        }
    }
}
