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
            <div class="value">Rp <fmt:formatNumber value="${saldoTotal}"/></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="panel stat">
            <div class="label"><i class="bi bi-arrow-down-left"></i> Pemasukan</div>
            <div class="value">Rp <fmt:formatNumber value="${pemasukan}"/></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="panel stat">
            <div class="label"><i class="bi bi-arrow-up-right"></i> Pengeluaran</div>
            <div class="value">Rp <fmt:formatNumber value="${pengeluaran}"/></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="panel stat">
            <div class="label"><i class="bi bi-plus-slash-minus"></i> Selisih periode</div>
            <div class="value">Rp <fmt:formatNumber value="${saldo}"/></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-8 order-2 order-xl-1">
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
                                ${t.pemasukan ? '+' : '&minus;'} Rp <fmt:formatNumber value="${t.jumlah}"/>
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
    </div>

    <div class="col-xl-4 order-1 order-xl-2">
        <div class="panel">
            <div class="panel-head"><h2>Catat pengeluaran</h2></div>
            <form class="panel-body" method="post" action="${ctx}/kas">
                <input type="hidden" name="filterBulan" value="${bulan}">
                <div class="mb-3">
                    <label class="form-label" for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" value="${hariIni}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="kategori">Kategori</label>
                    <input type="text" id="kategori" name="kategori" class="form-control" list="kategoriList" required maxlength="50"
                           placeholder="Sewa Lapangan">
                    <datalist id="kategoriList">
                        <option value="Sewa Lapangan"><option value="Pembelian Kok"><option value="Konsumsi"><option value="Lainnya">
                    </datalist>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="ket">Keterangan</label>
                    <input type="text" id="ket" name="keterangan" class="form-control" maxlength="255">
                </div>
                <div class="mb-4">
                    <label class="form-label" for="jml">Nominal</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">Rp</span>
                        <input type="number" id="jml" name="jumlah" class="form-control" min="1" required>
                    </div>
                </div>
                <button class="btn btn-ink w-100">Simpan Pengeluaran</button>
            </form>
        </div>

        <c:if test="${not empty daftarPengeluaran}">
            <div class="panel mt-4">
                <div class="panel-head"><h2>Daftar pengeluaran</h2></div>
                <table class="tbl">
                    <tbody>
                    <c:forEach var="p" items="${daftarPengeluaran}">
                        <tr>
                            <td>
                                <c:out value="${p.kategori}"/>
                                <div class="subtle" style="font-size:12.5px">${p.tanggal} &middot; Rp <fmt:formatNumber value="${p.jumlah}"/></div>
                            </td>
                            <td class="num">
                                <form method="post" action="${ctx}/kas" onsubmit="return confirm('Hapus pengeluaran ini?');">
                                    <input type="hidden" name="aksi" value="hapus">
                                    <input type="hidden" name="id" value="${p.id}">
                                    <input type="hidden" name="filterBulan" value="${bulan}">
                                    <button class="btn btn-line btn-icon danger" title="Hapus"><i class="bi bi-trash3"></i></button>
                                </form>
                            </td>
                        </tr>
                    </c:forEach>
                    </tbody>
                </table>
            </div>
        </c:if>
    </div>
</div>
<%@ include file="_footer.jsp" %>
