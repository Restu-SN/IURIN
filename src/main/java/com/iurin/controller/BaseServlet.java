package com.iurin.controller;

import java.io.IOException;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/** Helper bersama untuk semua servlet Iurin. */
public abstract class BaseServlet extends HttpServlet {

    protected void tampil(HttpServletRequest req, HttpServletResponse resp, String view, String menu)
            throws ServletException, IOException {
        req.setAttribute("menu", menu);
        req.getRequestDispatcher("/WEB-INF/views/" + view + ".jsp").forward(req, resp);
    }

    /** Pesan sekali tampil setelah redirect. tipe: success / danger / warning. */
    protected void flash(HttpServletRequest req, String pesan, String tipe) {
        req.getSession().setAttribute("flash", pesan);
        req.getSession().setAttribute("flashTipe", tipe);
    }

    protected void redirect(HttpServletRequest req, HttpServletResponse resp, String path) throws IOException {
        resp.sendRedirect(req.getContextPath() + path);
    }

    protected static String param(HttpServletRequest req, String nama) {
        String v = req.getParameter(nama);
        return v == null ? "" : v.trim();
    }

    protected static int angka(HttpServletRequest req, String nama) {
        try {
            return Integer.parseInt(param(req, nama).replace(".", ""));
        } catch (NumberFormatException e) {
            return 0;
        }
    }
}
