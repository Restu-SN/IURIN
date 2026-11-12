package com.iurin.controller;

import com.iurin.model.Pengguna;
import com.iurin.util.Format;
import java.io.IOException;
import java.time.LocalDate;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

/** Helper bersama untuk semua servlet Iurin. */
public abstract class BaseServlet extends HttpServlet {

    protected void tampil(HttpServletRequest req, HttpServletResponse resp, String view, String menu)
            throws ServletException, IOException {
        req.setAttribute("menu", menu);
        req.setAttribute("isAdmin", isAdmin(req));
        req.getRequestDispatcher("/WEB-INF/views/" + view + ".jsp").forward(req, resp);
    }

    /** Pesan sekali tampil setelah redirect. tipe: success / danger / warning. */
    protected void flash(HttpServletRequest req, String pesan, String tipe) {
        req.getSession().setAttribute("flash", pesan);
        req.getSession().setAttribute("flashTipe", tipe);
    }

    protected void flash(HttpServletRequest req, String pesan) {
        flash(req, pesan, "success");
    }

    protected void redirect(HttpServletRequest req, HttpServletResponse resp, String path) throws IOException {
        resp.sendRedirect(req.getContextPath() + path);
    }

    protected static Pengguna pengguna(HttpServletRequest req) {
        HttpSession s = req.getSession(false);
        return s == null ? null : (Pengguna) s.getAttribute("pengguna");
    }

    protected static boolean isAdmin(HttpServletRequest req) {
        Pengguna p = pengguna(req);
        return p != null && p.isAdmin();
    }

    protected static String param(HttpServletRequest req, String nama) {
        String v = req.getParameter(nama);
        return v == null ? "" : v.trim();
    }

    /** Angka dari form; titik ribuan dibuang ("15.000" menjadi 15000). */
    protected static int angka(HttpServletRequest req, String nama) {
        return angka(req.getParameter(nama));
    }

    protected static int angka(String teks) {
        try {
            return teks == null ? 0 : Integer.parseInt(teks.trim().replace(".", ""));
        } catch (NumberFormatException e) {
            return 0;
        }
    }

    /** Tanggal dari parameter, atau null kalau tidak valid. */
    protected static LocalDate tanggal(HttpServletRequest req, String nama) {
        return Format.tanggalDari(param(req, nama));
    }
}
