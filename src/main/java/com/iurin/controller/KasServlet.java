package com.iurin.controller;

import com.iurin.model.Anggota;
import com.iurin.model.AnggotaDao;
import com.iurin.model.KasDao;
import com.iurin.model.KasHari;
import com.iurin.model.Pembayaran;
import com.iurin.model.PembayaranDao;
import com.iurin.model.Pengeluaran;
import com.iurin.model.RincianTalangan;
import com.iurin.model.TalanganDao;
import com.iurin.util.Format;
import java.io.IOException;
import java.time.LocalDate;
import java.time.LocalTime;
import java.time.ZoneId;
import java.util.HashMap;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.hibernate.HibernateException;

/** Rekap kas klub (hanya lihat) dan struk per hari main. */
@WebServlet({"/kas", "/kas/struk"})
public class KasServlet extends BaseServlet {

    private final KasDao kasDao = new KasDao();
    private final PembayaranDao pembayaranDao = new PembayaranDao();
    private final TalanganDao talanganDao = new TalanganDao();
    private final AnggotaDao anggotaDao = new AnggotaDao();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        try {
            if (req.getServletPath().equals("/kas/struk")) {
                struk(req, resp);
                return;
            }
            String bulan = param(req, "bulan");
            if (!Format.bulanValid(bulan)) {
                bulan = "";
            }
            long masuk = kasDao.totalPemasukan(bulan);
            long keluar = kasDao.totalPengeluaran(bulan);
            req.setAttribute("bulan", bulan);
            req.setAttribute("pemasukan", masuk);
            req.setAttribute("pengeluaran", keluar);
            req.setAttribute("saldo", masuk - keluar);
            req.setAttribute("saldoTotal", kasDao.totalPemasukan("") - kasDao.totalPengeluaran(""));
            req.setAttribute("transaksi", kasDao.transaksi(bulan));
            req.setAttribute("hariMain", pembayaranDao.sesiBulan(bulan));
            tampil(req, resp, "kas", "kas");
        } catch (HibernateException e) {
            throw new ServletException(e);
        }
    }

    /** Hapus struk = hapus semua catatan tanggal itu (yang main, guest, pengeluaran, kas minggu lalu). */
    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        LocalDate tanggal = tanggal(req, "tanggal");
        if (req.getServletPath().equals("/kas/struk") && param(req, "aksi").equals("hapus") && tanggal != null) {
            try {
                kasDao.hapusHari(tanggal);
            } catch (HibernateException e) {
                throw new ServletException(e);
            }
            flash(req, "Struk " + Format.namaTanggal(tanggal) + " beserta catatannya sudah dihapus.");
            redirect(req, resp, "/kas?bulan=" + tanggal.toString().substring(0, 7));
            return;
        }
        redirect(req, resp, "/kas");
    }

    /** Struk satu hari main: siapa yang bayar, pengeluaran, sisa, dan total kas. */
    private void struk(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        LocalDate tanggal = tanggal(req, "tanggal");
        List<Pembayaran> hadir = tanggal == null ? List.of() : pembayaranDao.daftarTanggal(tanggal);
        List<Pengeluaran> pengeluaran = tanggal == null ? List.of() : kasDao.daftarPengeluaranTanggal(tanggal);
        KasHari kasHari = tanggal == null ? null : kasDao.kasHari(tanggal);
        if (hadir.isEmpty() && pengeluaran.isEmpty() && kasHari == null) {
            flash(req, "Belum ada catatan main di tanggal itu.", "warning");
            redirect(req, resp, "/kas");
            return;
        }
        int kasLalu = kasHari == null ? 0 : kasHari.getKasLalu();
        int guest = kasHari == null ? 0 : kasHari.getGuest();
        int masuk = hadir.stream().mapToInt(Pembayaran::getJumlah).sum() + (kasHari == null ? 0 : kasHari.getMasukGuest());
        int keluar = KasDao.jumlah(pengeluaran);
        List<List<Pengeluaran>> pisah = KasDao.pisahKasLalu(pengeluaran);

        // Sisa talangan per anggota sampai tanggal struk ini
        Map<Integer, String> nama = new HashMap<>();
        for (Anggota a : anggotaDao.semua()) {
            nama.put(a.getId(), a.getNama());
        }
        Map<String, Long> talangan = new LinkedHashMap<>();
        for (Map.Entry<Integer, RincianTalangan> e : talanganDao.rincian(tanggal).entrySet()) {
            if (e.getValue().getTotal() > 0 && nama.containsKey(e.getKey())) {
                talangan.put(nama.get(e.getKey()), e.getValue().getSisa());
            }
        }

        req.setAttribute("tanggal", tanggal);
        req.setAttribute("tanggalTeks", tanggal.format(java.time.format.DateTimeFormatter.ofPattern("dd-MM-yyyy")));
        req.setAttribute("nomor", "IUR-" + tanggal.toString().replace("-", ""));
        req.setAttribute("dicetak", LocalTime.now(ZoneId.of("Asia/Jakarta")).toString().substring(0, 5));
        req.setAttribute("hadir", hadir);
        req.setAttribute("masuk", masuk);
        req.setAttribute("keluar", keluar);
        req.setAttribute("kasLalu", kasLalu);
        req.setAttribute("guest", guest);
        req.setAttribute("tarifGuest", kasHari == null ? 0 : kasHari.getTarifGuest());
        req.setAttribute("dariKas", pisah.get(0));
        req.setAttribute("keluarKas", KasDao.jumlah(pisah.get(0)));
        req.setAttribute("pengeluaranHari", pisah.get(1));
        req.setAttribute("keluarHari", KasDao.jumlah(pisah.get(1)));
        req.setAttribute("talangan", talangan);
        tampil(req, resp, "kas-struk", "kas");
    }
}
