<%@ include file="_header.jsp" %>
<c:set var="edit" value="${anggota != null and anggota.id > 0}"/>
<div class="page-head">
    <div>
        <div class="eyebrow"><a href="${ctx}/anggota" class="text-decoration-none text-reset"><i class="bi bi-arrow-left"></i> Anggota</a></div>
        <h1>${edit ? 'Edit Anggota' : 'Tambah Anggota'}</h1>
    </div>
</div>

<div class="panel" style="max-width:520px">
    <form class="panel-body" method="post" action="${ctx}/anggota">
        <input type="hidden" name="aksi" value="simpan">
        <input type="hidden" name="id" value="${edit ? anggota.id : 0}">
        <div class="mb-3">
            <label class="form-label" for="nama">Nama</label>
            <input type="text" id="nama" name="nama" class="form-control" required maxlength="100" autofocus
                   value="<c:out value='${anggota.nama}'/>">
        </div>
        <div class="mb-4">
            <label class="form-label" for="nominalIuran">Iuran sekali main</label>
            <div class="input-group">
                <span class="input-group-text bg-white">Rp</span>
                <input type="text" id="nominalIuran" name="nominalIuran" class="form-control input-rupiah" inputmode="numeric" required
                       value="${f:angka(anggota != null ? anggota.nominalIuran : 15000)}">
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-ink">Simpan</button>
            <a href="${ctx}/anggota" class="btn btn-line">Batal</a>
        </div>
    </form>
</div>

<c:if test="${edit}">
<div class="panel mt-4" style="max-width:520px">
    <div class="panel-head"><h2>Talangan</h2><span class="subtle">Sisa ${f:rupiah(rincianTalangan.sisa)}</span></div>
    <div class="panel-body">
        <p class="subtle mb-3">Uang klub yang dibayari dulu oleh <c:out value="${anggota.nama}"/> (mis. sewa lapangan).
            Tiap dia main, iurannya memotong talangan sampai habis.</p>
        <div class="kas-hitung mb-4">
            <div><span>Total ditalangin</span><span>${f:rupiah(rincianTalangan.total)}</span></div>
            <div><span>Sudah terpotong dari main</span><span>&minus; ${f:rupiah(rincianTalangan.terpotong)}</span></div>
            <div class="kas-total"><span>Sisa talangan</span><span>${f:rupiah(rincianTalangan.sisa)}</span></div>
        </div>
        <form method="post" action="${ctx}/anggota" class="mb-3">
            <input type="hidden" name="aksi" value="tambahTalangan">
            <input type="hidden" name="id" value="${anggota.id}">
            <div class="row g-2">
                <div class="col-sm-6">
                    <label class="form-label" for="jumlahTalangan">Nominal</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">Rp</span>
                        <input type="text" id="jumlahTalangan" name="jumlah" class="form-control input-rupiah" inputmode="numeric" placeholder="150.000" required>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="tanggalTalangan">Tanggal</label>
                    <input type="date" id="tanggalTalangan" name="tanggal" class="form-control" value="${hariIni}">
                </div>
                <div class="col-12">
                    <label class="form-label" for="ketTalangan">Untuk apa</label>
                    <input type="text" id="ketTalangan" name="keterangan" class="form-control" maxlength="255" value="Sewa lapangan">
                </div>
            </div>
            <button class="btn btn-ink w-100 mt-3">Tambah Talangan</button>
        </form>
    </div>
    <c:if test="${not empty daftarTalangan}">
        <table class="tbl" style="border-top:var(--line)">
            <tbody>
            <c:forEach var="t" items="${daftarTalangan}">
                <tr>
                    <td>
                        <c:out value="${empty t.keterangan ? 'Talangan' : t.keterangan}"/>
                        <div class="subtle" style="font-size:12.5px">${f:namaTanggal(t.tanggal)}</div>
                    </td>
                    <td class="num text-nowrap">${f:rupiah(t.jumlah)}</td>
                    <td class="num" style="width:1%">
                        <form method="post" action="${ctx}/anggota" onsubmit="return confirm('Hapus catatan talangan ini?');">
                            <input type="hidden" name="aksi" value="hapusTalangan">
                            <input type="hidden" name="talanganId" value="${t.id}">
                            <button class="btn btn-line btn-icon danger" title="Hapus"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>
            </c:forEach>
            </tbody>
        </table>
    </c:if>
</div>
</c:if>
<%@ include file="_footer.jsp" %>
