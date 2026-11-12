<%@ include file="_header.jsp" %>
<div class="page-head">
    <div>
        <h1>Anggota</h1>
    </div>
    <c:if test="${isAdmin}">
        <a href="${ctx}/anggota?aksi=tambah" class="btn btn-ink"><i class="bi bi-plus-lg me-1"></i> Tambah Anggota</a>
    </c:if>
</div>

<div class="panel">
    <div class="panel-head">
        <form class="input-search flex-grow-1" method="get" action="${ctx}/anggota" style="max-width:420px">
            <i class="bi bi-search"></i>
            <input type="search" name="q" class="form-control" placeholder="Cari nama anggota" value="<c:out value='${q}'/>">
        </form>
        <span class="subtle d-none d-sm-inline">${fn:length(daftar)} anggota</span>
    </div>
    <div class="table-responsive">
        <table class="tbl">
            <thead>
            <tr>
                <th>Nama</th>
                <th class="num wide">Iuran / main</th>
                <th>Bulan ini</th>
                <c:if test="${isAdmin}"><th class="num"></th></c:if>
            </tr>
            </thead>
            <tbody>
            <c:forEach var="a" items="${daftar}">
                <c:set var="r" value="${rekap[a.id]}"/>
                <tr>
                    <td>
                        <div class="person">
                            <span class="avatar">${f:inisial(a.nama)}</span>
                            <div>
                                <c:out value="${a.nama}"/>
                                <span class="meta">${f:rupiah(a.nominalIuran)}/main</span>
                                <c:if test="${talangan[a.id] > 0}">
                                    <span class="pill pill-belum mt-1" style="font-size:11px">Talangan ${f:rupiah(talangan[a.id])}</span>
                                </c:if>
                            </div>
                        </div>
                    </td>
                    <td class="num wide">${f:rupiah(a.nominalIuran)}</td>
                    <td>
                        <c:choose>
                            <c:when test="${r.main > 0}"><span class="pill pill-lunas">${r.main}&times; main</span></c:when>
                            <c:otherwise><span class="pill pill-belum">Belum main</span></c:otherwise>
                        </c:choose>
                        <c:if test="${r.terakhir != null}">
                            <div class="subtle" style="font-size:12px;margin-top:4px">Terakhir ${f:namaTanggal(r.terakhir)}</div>
                        </c:if>
                    </td>
                    <c:if test="${isAdmin}">
                        <td class="num text-nowrap">
                            <form method="post" action="${ctx}/anggota" id="hapus${a.id}" class="d-inline"
                                  onsubmit="return confirm('Hapus anggota ini beserta seluruh riwayat mainnya?');">
                                <input type="hidden" name="aksi" value="hapus">
                                <input type="hidden" name="id" value="${a.id}">
                            </form>
                            <%-- Layar besar: dua tombol; HP: satu menu titik tiga --%>
                            <span class="d-none d-sm-inline">
                                <a href="${ctx}/anggota?aksi=edit&id=${a.id}" class="btn btn-line btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                                <button form="hapus${a.id}" class="btn btn-line btn-icon danger" title="Hapus"><i class="bi bi-trash3"></i></button>
                            </span>
                            <div class="dropdown d-sm-none">
                                <button class="btn btn-line btn-icon" data-bs-toggle="dropdown" aria-label="Aksi"><i class="bi bi-three-dots"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="${ctx}/anggota?aksi=edit&id=${a.id}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                    <li><button form="hapus${a.id}" class="dropdown-item"><i class="bi bi-trash3 me-2"></i>Hapus</button></li>
                                </ul>
                            </div>
                        </td>
                    </c:if>
                </tr>
            </c:forEach>
            <c:if test="${empty daftar}">
                <tr><td colspan="4" class="empty"><i class="bi bi-people fs-3 d-block mb-2"></i>Belum ada anggota.</td></tr>
            </c:if>
            </tbody>
        </table>
    </div>
</div>
<%@ include file="_footer.jsp" %>
