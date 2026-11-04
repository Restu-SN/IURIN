<%@ include file="_header.jsp" %>
<c:set var="edit" value="${not empty anggota and anggota.id > 0}"/>
<div class="page-head">
    <div>
        <div class="eyebrow"><a href="${ctx}/anggota" class="text-decoration-none text-reset"><i class="bi bi-arrow-left"></i> Anggota</a></div>
        <h1>${edit ? 'Edit Anggota' : 'Tambah Anggota'}</h1>
    </div>
</div>

<div class="panel" style="max-width:520px">
    <form class="panel-body" method="post" action="${ctx}/anggota">
        <input type="hidden" name="id" value="${edit ? anggota.id : 0}">
        <div class="mb-3">
            <label class="form-label" for="nama">Nama</label>
            <input type="text" id="nama" name="nama" class="form-control" required maxlength="100" autofocus
                   value="<c:out value='${anggota.nama}'/>">
        </div>
        <div class="mb-4">
            <label class="form-label" for="nominalIuran">Iuran per minggu</label>
            <div class="input-group">
                <span class="input-group-text bg-white">Rp</span>
                <input type="number" id="nominalIuran" name="nominalIuran" class="form-control" min="0" step="500" required
                       value="${empty anggota ? 15000 : anggota.nominalIuran}">
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-ink">Simpan</button>
            <a href="${ctx}/anggota" class="btn btn-line">Batal</a>
        </div>
    </form>
</div>
<%@ include file="_footer.jsp" %>
