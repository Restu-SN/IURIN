<%@ include file="_header.jsp" %>
<div class="page-head">
    <div>
        <div class="eyebrow"><c:out value="${namaBulan}"/></div>
        <h1>Riwayat Main</h1>
    </div>
    <form method="get" action="${ctx}/status">
        <input type="month" name="bulan" class="form-control" value="${bulan}" onchange="this.form.submit()" aria-label="Pilih bulan">
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="panel stat stat-ink">
            <div class="label"><i class="bi bi-calendar-check"></i> Hari main</div>
            <div class="value">${hariMain} <span class="fs-6 fw-normal text-secondary">kali</span></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="panel stat">
            <div class="label"><i class="bi bi-arrow-down-left"></i> Uang masuk</div>
            <div class="value">${f:rupiah(masuk)}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="panel stat">
            <div class="label"><i class="bi bi-arrow-up-right"></i> Pengeluaran</div>
            <div class="value">${f:rupiah(keluar)}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="panel stat">
            <div class="label"><i class="bi bi-plus-slash-minus"></i> Sisa bulan ini</div>
            <div class="value">${f:rupiah(masuk - keluar)}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="panel">
            <div class="panel-head"><h2>Per tanggal</h2><span class="subtle">${fn:length(sesi)} hari</span></div>
            <div class="table-responsive">
                <table class="tbl">
                    <thead><tr><th>Tanggal</th><th class="num">Masuk</th><th class="num">Keluar</th><th class="num">Sisa</th></tr></thead>
                    <tbody>
                    <c:forEach var="s" items="${sesi}">
                        <tr>
                            <td>
                                <a href="${ctx}/pembayaran?tanggal=${s.tanggal}" class="fw-semibold text-nowrap">${f:namaTanggal(s.tanggal)}</a>
                                <div class="hadir-nama">
                                    ${fn:length(s.hadir)} orang<c:if test="${not empty s.hadir}">: <c:out value="${s.hadirTeks}"/></c:if><c:if test="${s.guest > 0}"> + ${s.guest} guest</c:if>
                                    <c:if test="${not empty s.pengeluaran}"><br>Keluar: <c:out value="${s.pengeluaranTeks}"/></c:if>
                                </div>
                            </td>
                            <td class="num amount-in text-nowrap">${f:rupiah(s.masuk)}</td>
                            <td class="num amount-out text-nowrap">
                                <c:choose>
                                    <c:when test="${s.keluar > 0}">&minus; ${f:rupiah(s.keluar)}</c:when>
                                    <c:otherwise><span class="subtle">&mdash;</span></c:otherwise>
                                </c:choose>
                            </td>
                            <td class="num text-nowrap fw-bold">${f:rupiah(s.sisa)}</td>
                        </tr>
                    </c:forEach>
                    <c:if test="${empty sesi}">
                        <tr><td colspan="4" class="empty"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada catatan main bulan ini.</td></tr>
                    </c:if>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="panel">
            <div class="panel-head"><h2>Per anggota</h2><span class="subtle"><c:out value="${namaBulan}"/></span></div>
            <div class="table-responsive">
                <table class="tbl">
                    <thead><tr><th>Nama</th><th class="num">Main</th><th class="num">Bayar</th></tr></thead>
                    <tbody>
                    <c:forEach var="r" items="${anggota}">
                        <tr>
                            <td>
                                <div class="person">
                                    <span class="avatar ${r.main > 0 ? '' : 'avatar-line'}">${f:inisial(r.anggota.nama)}</span>
                                    <div>
                                        <c:out value="${r.anggota.nama}"/>
                                        <div class="subtle" style="font-size:12.5px">Terakhir: ${r.terakhir != null ? f:namaTanggal(r.terakhir) : 'belum pernah'}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="num fw-bold">${r.main}&times;</td>
                            <td class="num text-nowrap">
                                <c:choose>
                                    <c:when test="${r.total > 0}">${f:rupiah(r.total)}</c:when>
                                    <c:otherwise><span class="subtle">&mdash;</span></c:otherwise>
                                </c:choose>
                            </td>
                        </tr>
                    </c:forEach>
                    <c:if test="${empty anggota}">
                        <tr><td colspan="3" class="empty">Belum ada anggota.</td></tr>
                    </c:if>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<%@ include file="_footer.jsp" %>
