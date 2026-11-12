<%@ include file="_header.jsp" %>
<div class="page-head">
    <div>
        <div class="eyebrow">${empty bulan ? 'Semua periode' : 'Periode bulan'}</div>
        <h1>Rekap Kas</h1>
    </div>
    <form method="get" action="${ctx}/kas" class="d-flex gap-2">
        <input type="month" name="bulan" class="form-control" value="${bulan}" onchange="this.form.submit()" aria-label="Pilih bulan">
        <c:if test="${not empty bulan}"><a href="${ctx}/kas" class="btn btn-line text-nowrap">Semua</a></c:if>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="panel stat stat-ink">
            <div class="label"><i class="bi bi-safe"></i> Saldo kas</div>
            <div class="value">${f:rupiah(saldoTotal)}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="panel stat">
            <div class="label"><i class="bi bi-arrow-down-left"></i> Pemasukan</div>
            <div class="value">${f:rupiah(pemasukan)}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="panel stat">
            <div class="label"><i class="bi bi-arrow-up-right"></i> Pengeluaran</div>
            <div class="value">${f:rupiah(pengeluaran)}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="panel stat">
            <div class="label"><i class="bi bi-plus-slash-minus"></i> Selisih periode</div>
            <div class="value">${f:rupiah(saldo)}</div>
        </div>
    </div>
</div>

<div class="panel mb-4">
    <div class="panel-head"><h2>Struk hari main</h2><span class="subtle">${fn:length(hariMain)} hari</span></div>
    <div class="table-responsive">
        <table class="tbl">
            <tbody>
            <c:forEach var="s" items="${hariMain}">
                <tr>
                    <td>
                        <span class="fw-semibold text-nowrap">${f:namaTanggal(s.tanggal)}</span>
                        <div class="subtle" style="font-size:12.5px">${s.jumlahOrang} orang main &middot; sisa ${f:rupiah(s.sisa)}</div>
                    </td>
                    <td class="num text-nowrap">
                        <a href="${ctx}/kas/struk?tanggal=${s.tanggal}" class="btn btn-line btn-sm text-nowrap"><i class="bi bi-receipt me-1"></i> Struk</a>
                        <c:if test="${isAdmin}">
                            <form method="post" action="${ctx}/kas/struk" class="d-inline"
                                  onsubmit="return confirm('Hapus struk ${f:namaTanggal(s.tanggal)}?\n\nSemua catatan tanggal ini (yang main, guest, pengeluaran, kas minggu lalu) ikut terhapus dan tidak bisa dikembalikan.');">
                                <input type="hidden" name="aksi" value="hapus">
                                <input type="hidden" name="tanggal" value="${s.tanggal}">
                                <button class="btn btn-line btn-sm btn-icon danger" title="Hapus struk"><i class="bi bi-trash3"></i></button>
                            </form>
                        </c:if>
                    </td>
                </tr>
            </c:forEach>
            <c:if test="${empty hariMain}">
                <tr><td colspan="2" class="empty">Belum ada hari main di periode ini.</td></tr>
            </c:if>
            </tbody>
        </table>
    </div>
</div>

<div class="panel">
    <div class="panel-head"><h2>Transaksi</h2><span class="subtle">${fn:length(transaksi)} catatan</span></div>
    <div class="table-responsive">
        <table class="tbl">
            <thead><tr><th>Keterangan</th><th class="wide">Tanggal</th><th class="num">Jumlah</th></tr></thead>
            <tbody>
            <c:forEach var="t" items="${transaksi}">
                <tr>
                    <td>
                        <div class="person">
                            <span class="avatar ${t.pemasukan ? '' : 'avatar-line'}">
                                <i class="bi ${t.pemasukan ? 'bi-arrow-down-left' : 'bi-arrow-up-right'}"></i>
                            </span>
                            <div>
                                <c:out value="${t.keterangan}"/>
                                <div class="subtle" style="font-size:12.5px">${t.jenis}<span class="d-sm-none"> &middot; ${t.tanggal}</span></div>
                            </div>
                        </div>
                    </td>
                    <td class="text-nowrap wide">${t.tanggal}</td>
                    <td class="num ${t.pemasukan ? 'amount-in' : 'amount-out'}">
                        ${t.pemasukan ? '+' : '&minus;'} ${f:rupiah(t.jumlah)}
                    </td>
                </tr>
            </c:forEach>
            <c:if test="${empty transaksi}">
                <tr><td colspan="3" class="empty"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada transaksi.</td></tr>
            </c:if>
            </tbody>
        </table>
    </div>
</div>
<%@ include file="_footer.jsp" %>
