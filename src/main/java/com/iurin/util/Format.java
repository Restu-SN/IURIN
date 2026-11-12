package com.iurin.util;

import java.text.NumberFormat;
import java.time.LocalDate;
import java.time.YearMonth;
import java.time.ZoneId;
import java.time.format.DateTimeParseException;
import java.util.Locale;

/**
 * Helper format rupiah, tanggal, dan bulan berbahasa Indonesia.
 * Method publiknya juga dipakai di JSP lewat fungsi EL (WEB-INF/iurin.tld), mis. ${f:rupiah(x)}.
 */
public final class Format {

    private static final Locale ID = new Locale("id", "ID");
    private static final ZoneId JAKARTA = ZoneId.of("Asia/Jakarta");
    private static final String[] HARI = {"Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"};
    private static final String[] BULAN_PENDEK = {"Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"};
    private static final String[] BULAN = {"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli",
            "Agustus", "September", "Oktober", "November", "Desember"};

    private Format() {
    }

    /** 15000 menjadi "Rp 15.000". */
    public static String rupiah(long nilai) {
        return "Rp " + angka(nilai);
    }

    /** 15000 menjadi "15.000". */
    public static String angka(long nilai) {
        return NumberFormat.getIntegerInstance(ID).format(nilai);
    }

    public static LocalDate hariIni() {
        return LocalDate.now(JAKARTA);
    }

    /** "2026-11-07" menjadi tanggal; null kalau formatnya salah. */
    public static LocalDate tanggalDari(String teks) {
        if (teks == null || !teks.matches("\\d{4}-\\d{2}-\\d{2}")) {
            return null;
        }
        try {
            return LocalDate.parse(teks);
        } catch (DateTimeParseException e) {
            return null;
        }
    }

    public static boolean bulanValid(String bulan) {
        return bulan != null && bulan.matches("\\d{4}-(0[1-9]|1[0-2])");
    }

    /** "2026-11" menjadi "November 2026". */
    public static String namaBulan(String bulan) {
        YearMonth ym = YearMonth.parse(bulan);
        return BULAN[ym.getMonthValue() - 1] + " " + ym.getYear();
    }

    /** Tanggal lengkap dengan hari, mis. "Sabtu, 7 Nov 2026". */
    public static String namaTanggal(LocalDate t) {
        if (t == null) {
            return "";
        }
        return HARI[t.getDayOfWeek().getValue() - 1] + ", " + t.getDayOfMonth() + " "
                + BULAN_PENDEK[t.getMonthValue() - 1] + " " + t.getYear();
    }

    /** Nama hari saja, mis. "Sabtu". */
    public static String namaHari(LocalDate t) {
        return t == null ? "" : HARI[t.getDayOfWeek().getValue() - 1];
    }

    /** "Bang Agil" menjadi "B". */
    public static String inisial(String nama) {
        return nama == null || nama.isEmpty() ? "" : nama.substring(0, 1).toUpperCase(ID);
    }
}
