package com.iurin.controller;

import com.iurin.model.AdminDao;
import com.iurin.model.Admin;
import java.io.IOException;
import org.hibernate.HibernateException;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

@WebServlet({"/login", "/logout"})
public class LoginServlet extends BaseServlet {

    private final AdminDao adminDao = new AdminDao();

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
            Admin admin = adminDao.login(param(req, "username"), req.getParameter("password"));
            if (admin == null) {
                req.setAttribute("error", "Username atau password salah.");
                req.getRequestDispatcher("/WEB-INF/views/login.jsp").forward(req, resp);
                return;
            }
            // Session baru setelah login (mencegah session fixation)
            HttpSession lama = req.getSession(false);
            if (lama != null) {
                lama.invalidate();
            }
            req.getSession(true).setAttribute("admin", admin);
            redirect(req, resp, "/anggota");
        } catch (HibernateException e) {
            throw new ServletException("Gagal terhubung ke database: " + e.getMessage(), e);
        }
    }
}
