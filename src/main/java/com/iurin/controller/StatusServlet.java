package com.iurin.controller;

import com.iurin.model.Anggota;
import com.iurin.model.AnggotaDao;
import com.iurin.model.PembayaranDao;
import com.iurin.model.RekapAnggota;
import com.iurin.model.Sesi;
import com.iurin.util.Format;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Comparator;
import java.util.List;
import java.util.Map;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.hibernate.HibernateException;

/** Riwayat Main: ringkasan tiap hari main dan rekap per anggota dalam sebulan. */
@WebServlet("/status")
public class StatusServlet extends BaseServlet {

    private final PembayaranDao pembayaranDao = new PembayaranDao();
    private final AnggotaDao anggotaDao = new AnggotaDao();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        try {
            String bulan = param(req, "bulan");
            if (!Format.bulanValid(bulan)) {
                bulan = Format.hariIni().toString().substring(0, 7);
            }
            List<Sesi> sesi = pembayaranDao.sesiBulan(bulan);
            Map<Integer, RekapAnggota> rekap = pembayaranDao.rekapAnggota(bulan);

            List<RekapAnggota> anggota = new ArrayList<>();
            for (Anggota a : anggotaDao.semua()) {
                RekapAnggota r = rekap.get(a.getId());
                anggota.add(r != null ? r : new RekapAnggota(a));
            }
            // Yang paling sering main di atas
            anggota.sort(Comparator.comparingInt(RekapAnggota::getMain).reversed()
                    .thenComparing(r -> r.getAnggota().getNama()));

            req.setAttribute("bulan", bulan);
            req.setAttribute("namaBulan", Format.namaBulan(bulan));
            req.setAttribute("sesi", sesi);
            req.setAttribute("anggota", anggota);
            req.setAttribute("hariMain", sesi.stream().filter(s -> !s.getHadir().isEmpty() || s.getGuest() > 0).count());
            req.setAttribute("masuk", sesi.stream().mapToInt(Sesi::getMasuk).sum());
            req.setAttribute("keluar", sesi.stream().mapToInt(Sesi::getKeluar).sum());
            tampil(req, resp, "status", "status");
        } catch (HibernateException e) {
            throw new ServletException(e);
        }
    }
}
