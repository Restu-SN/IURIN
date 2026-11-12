package com.iurin.model;

/** Talangan satu anggota: total ditalangin, yang sudah terpotong dari main, dan sisanya. */
public class RincianTalangan {

    private long total;
    private long terpotong;

    public long getTotal() { return total; }
    void setTotal(long total) { this.total = total; }

    public long getTerpotong() { return terpotong; }
    void setTerpotong(long terpotong) { this.terpotong = terpotong; }

    public long getSisa() { return Math.max(0, total - terpotong); }
}
