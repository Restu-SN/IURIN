<%@ include file="_header.jsp" %>
<c:set var="total" value="${jumlahLunas + jumlahBelum}"/>
<div class="page-head">
    <div>
        <div class="eyebrow">${namaMinggu}</div>
        <h1>Status Iuran</h1>
    </div>
    <form method="get" action="${ctx}/status">
        <input type="week" name="minggu" class="form-control" value="${minggu}" onchange="this.form.submit()" aria-label="Pilih minggu">
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-sm-4">
        <div class="panel stat stat-ink">
            <div class="label"><i class="bi bi-check-circle"></i> Sudah lunas</div>
            <div class="value">${jumlahLunas} <span class="fs-6 fw-normal text-secondary">/ ${total}</span></div>
        </div>
    </div>
    <div class="col-6 col-sm-4">
        <div class="panel stat">
            <div class="label"><i class="bi bi-hourglass-split"></i> Belum bayar</div>
            <div class="value">${jumlahBelum}</div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="panel stat">
            <div class="label"><i class="bi bi-pie-chart"></i> Kepatuhan</div>
            <div class="value"><fmt:formatNumber value="${total == 0 ? 0 : jumlahLunas * 100 / total}" maxFractionDigits="0"/>%</div>
            <div class="meter mt-2"><span style="width:${total == 0 ? 0 : jumlahLunas * 100 / total}%"></span></div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="tbl">
            <thead><tr><th>Nama</th><th>Status</th><th class="num wide">Dibayar</th></tr></thead>
            <tbody>
            <c:forEach var="s" items="${daftar}">
                <tr>
                    <td>
                        <div class="person">
                            <span class="avatar ${s.status == 'LUNAS' ? '' : 'avatar-line'}">${fn:substring(s.anggota.nama, 0, 1)}</span>
                            <div>
                                <c:out value="${s.anggota.nama}"/>
                                <c:if test="${not empty s.pembayaran}">
                                    <span class="meta">Rp <fmt:formatNumber value="${s.pembayaran.jumlah}"/> &middot; ${s.pembayaran.tanggalBayar}</span>
                                </c:if>
                            </div>
                        </div>
                    </td>
                    <td>
                        <c:choose>
                            <c:when test="${s.status == 'LUNAS'}"><span class="pill pill-lunas"><i class="bi bi-check-lg"></i> Lunas</span></c:when>
                            <c:when test="${s.status == 'MENUNGGAK'}"><span class="pill pill-tunggak"><i class="bi bi-exclamation-lg"></i> Menunggak</span></c:when>
                            <c:otherwise><span class="pill pill-belum"><i class="bi bi-clock"></i> Belum bayar</span></c:otherwise>
                        </c:choose>
                    </td>
                    <td class="num wide">
                        <c:choose>
                            <c:when test="${not empty s.pembayaran}">
                                <span class="amount-in">Rp <fmt:formatNumber value="${s.pembayaran.jumlah}"/></span>
                                <div class="subtle" style="font-size:12.5px">${s.pembayaran.tanggalBayar}</div>
                            </c:when>
                            <c:otherwise><span class="subtle">&mdash;</span></c:otherwise>
                        </c:choose>
                    </td>
                </tr>
            </c:forEach>
            <c:if test="${empty daftar}">
                <tr><td colspan="3" class="empty">Belum ada anggota.</td></tr>
            </c:if>
            </tbody>
        </table>
    </div>
</div>
<p class="subtle mt-3 mb-0" style="font-size:13px">
    <span class="pill pill-lunas me-1">Lunas</span> sudah bayar &nbsp;
    <span class="pill pill-belum me-1">Belum bayar</span> minggu berjalan &nbsp;
    <span class="pill pill-tunggak me-1">Menunggak</span> minggu sudah lewat
</p>
<%@ include file="_footer.jsp" %>
