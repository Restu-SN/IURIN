package com.iurin.controller;

import com.iurin.model.Pengguna;
import com.iurin.model.PenggunaDao;
import java.io.IOException;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import org.hibernate.HibernateException;

@WebServlet({"/login", "/logout"})
public class LoginServlet extends BaseServlet {

    private final PenggunaDao penggunaDao = new PenggunaDao();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        if (req.getServletPath().equals("/logout")) {
            HttpSession s = req.getSession(false);
            if (s != null) {
                s.invalidate();
            }
            redirect(req, resp, "/login");
            return;
        }
        req.getRequestDispatcher("/WEB-INF/views/login.jsp").forward(req, resp);
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        try {
            Pengguna p = penggunaDao.login(param(req, "username"), req.getParameter("password"));
            if (p == null) {
                req.setAttribute("error", "Username atau password salah.");
                req.getRequestDispatcher("/WEB-INF/views/login.jsp").forward(req, resp);
                return;
            }
            // Session baru setelah login (mencegah session fixation)
            HttpSession lama = req.getSession(false);
            if (lama != null) {
                lama.invalidate();
            }
            req.getSession(true).setAttribute("pengguna", p);
            redirect(req, resp, "/anggota");
        } catch (HibernateException e) {
            throw new ServletException("Gagal terhubung ke database: " + e.getMessage(), e);
        }
    }
}
