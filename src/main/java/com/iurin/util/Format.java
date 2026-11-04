package com.iurin.util;

import java.text.NumberFormat;
import java.time.DayOfWeek;
import java.time.LocalDate;
import java.time.YearMonth;
import java.time.format.DateTimeFormatter;
import java.time.format.TextStyle;
import java.time.temporal.IsoFields;
import java.util.Locale;

/** Helper format rupiah, minggu (ISO YYYY-Www), dan bulan berbahasa Indonesia. */
public final class Format {

    private static final Locale ID = new Locale("id", "ID");
    private static final DateTimeFormatter TGL = DateTimeFormatter.ofPattern("d MMM", ID);
    private static final DateTimeFormatter TGL_TAHUN = DateTimeFormatter.ofPattern("d MMM yyyy", ID);

    private Format() {
    }

    public static String rupiah(long nilai) {
        return "Rp " + NumberFormat.getIntegerInstance(ID).format(nilai);
    }

    /** "2026-11" menjadi "November 2026". */
    public static String namaBulan(String bulan) {
        YearMonth ym = YearMonth.parse(bulan);
        return ym.getMonth().getDisplayName(TextStyle.FULL, ID) + " " + ym.getYear();
    }

    /** Kode minggu ISO untuk tanggal tertentu, mis. "2026-W45". */
    public static String mingguDari(LocalDate tgl) {
        return String.format("%d-W%02d", tgl.get(IsoFields.WEEK_BASED_YEAR), tgl.get(IsoFields.WEEK_OF_WEEK_BASED_YEAR));
    }

    public static String mingguIni() {
        return mingguDari(LocalDate.now());
    }

    public static boolean mingguValid(String minggu) {
        return minggu != null && minggu.matches("\\d{4}-W\\d{2}");
    }

    /** Hari Senin dari minggu "YYYY-Www". */
    public static LocalDate senin(String minggu) {
        int tahun = Integer.parseInt(minggu.substring(0, 4));
        int ke = Integer.parseInt(minggu.substring(6));
        return LocalDate.of(tahun, 1, 4).with(IsoFields.WEEK_OF_WEEK_BASED_YEAR, ke).with(DayOfWeek.MONDAY);
    }

    /** "2026-W45" menjadi "Minggu 45 (2 Nov - 8 Nov 2026)". */
    public static String namaMinggu(String minggu) {
        LocalDate awal = senin(minggu);
        LocalDate akhir = awal.plusDays(6);
        return "Minggu " + Integer.parseInt(minggu.substring(6))
                + " (" + awal.format(TGL) + " - " + akhir.format(TGL_TAHUN) + ")";
    }
}
