package com.iurin.controller;

import com.iurin.model.Pengguna;
import java.io.IOException;
import javax.servlet.Filter;
import javax.servlet.FilterChain;
import javax.servlet.FilterConfig;
import javax.servlet.ServletException;
import javax.servlet.ServletRequest;
import javax.servlet.ServletResponse;
import javax.servlet.annotation.WebFilter;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

/**
 * Semua halaman wajib login, kecuali halaman login dan file aset.
 * Akun anggota hanya boleh melihat: semua POST (kecuali login & ganti password)
 * dan halaman tambah/edit anggota khusus admin.
 */
@WebFilter("/*")
public class AuthFilter implements Filter {

    // init/destroy wajib ditulis karena Tomcat 8.5 (Servlet 3.1) belum punya default method
    @Override
    public void init(FilterConfig config) {
    }

    @Override
    public void destroy() {
    }

    @Override
    public void doFilter(ServletRequest request, ServletResponse response, FilterChain chain)
            throws IOException, ServletException {
        HttpServletRequest req = (HttpServletRequest) request;
        HttpServletResponse resp = (HttpServletResponse) response;
        req.setCharacterEncoding("UTF-8");

        String path = req.getRequestURI().substring(req.getContextPath().length());
        if (path.equals("/login") || path.startsWith("/assets/")) {
            chain.doFilter(request, response);
            return;
        }
        HttpSession session = req.getSession(false);
        Pengguna p = session == null ? null : (Pengguna) session.getAttribute("pengguna");
        if (p == null) {
            resp.sendRedirect(req.getContextPath() + "/login");
            return;
        }

        String aksi = req.getParameter("aksi");
        boolean ubahData = req.getMethod().equals("POST") && !path.equals("/password");
        boolean halamanAdmin = path.equals("/anggota") && ("tambah".equals(aksi) || "edit".equals(aksi));
        if (!p.isAdmin() && (ubahData || halamanAdmin)) {
            session.setAttribute("flash", "Akun Anda hanya bisa melihat data.");
            session.setAttribute("flashTipe", "warning");
            String asal = req.getHeader("Referer");
            resp.sendRedirect(asal != null ? asal : req.getContextPath() + "/anggota");
            return;
        }
        chain.doFilter(request, response);
    }
}
