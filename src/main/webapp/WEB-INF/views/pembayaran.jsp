<%@ include file="_header.jsp" %>
<div class="page-head">
    <div>
        <div class="eyebrow">Iuran mingguan</div>
        <h1>Pembayaran</h1>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="panel">
            <div class="panel-head"><h2>Catat pembayaran</h2></div>
            <form class="panel-body" method="post" action="${ctx}/pembayaran">
                <div class="mb-3">
                    <label class="form-label" for="anggotaId">Anggota</label>
                    <select name="anggotaId" id="anggotaId" class="form-select" required>
                        <option value="">Pilih anggota</option>
                        <c:forEach var="a" items="${daftarAnggota}">
                            <option value="${a.id}" data-iuran="${a.nominalIuran}"
                                ${input.anggotaId == a.id ? 'selected' : ''}><c:out value="${a.nama}"/></option>
                        </c:forEach>
                    </select>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6 col-lg-12 col-xl-6">
                        <label class="form-label" for="mingguBayar">Minggu iuran</label>
                        <input type="week" id="mingguBayar" name="minggu" class="form-control" required placeholder="2026-W45"
                               value="${empty input ? minggu : input.minggu}">
                    </div>
                    <div class="col-sm-6 col-lg-12 col-xl-6">
                        <label class="form-label" for="tanggalBayar">Tanggal bayar</label>
                        <input type="date" id="tanggalBayar" name="tanggalBayar" class="form-control" required
                               value="${empty input.tanggalBayar ? hariIni : input.tanggalBayar}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="jumlah">Nominal</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">Rp</span>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" required
                               value="${input.jumlah > 0 ? input.jumlah : ''}">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="keterangan">Keterangan</label>
                    <input type="text" id="keterangan" name="keterangan" class="form-control" maxlength="255" placeholder="Tunai / Transfer"
                           value="<c:out value='${input.keterangan}'/>">
                </div>
                <button class="btn btn-ink w-100">Simpan Pembayaran</button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="panel">
            <div class="panel-head flex-wrap">
                <div>
                    <h2>Riwayat</h2>
                    <div class="subtle">${namaMinggu}</div>
                </div>
                <form method="get" action="${ctx}/pembayaran">
                    <input type="week" name="minggu" class="form-control form-control-sm" value="${minggu}"
                           onchange="this.form.submit()" aria-label="Pilih minggu">
                </form>
            </div>
            <div class="table-responsive">
                <table class="tbl">
                    <thead><tr><th>Anggota</th><th class="wide">Tanggal</th><th class="num">Jumlah</th><th></th></tr></thead>
                    <tbody>
                    <c:forEach var="p" items="${riwayat}">
                        <tr>
                            <td>
                                <div class="person">
                                    <span class="avatar">${fn:substring(p.namaAnggota, 0, 1)}</span>
                                    <div>
                                        <c:out value="${p.namaAnggota}"/>
                                        <div class="subtle" style="font-size:12.5px"><c:out value="${empty p.keterangan ? '-' : p.keterangan}"/><span class="d-sm-none"> &middot; ${p.tanggalBayar}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-nowrap wide">${p.tanggalBayar}</td>
                            <td class="num amount-in">Rp <fmt:formatNumber value="${p.jumlah}"/></td>
                            <td class="num">
                                <form method="post" action="${ctx}/pembayaran" onsubmit="return confirm('Hapus pembayaran ini?');">
                                    <input type="hidden" name="aksi" value="hapus">
                                    <input type="hidden" name="id" value="${p.id}">
                                    <input type="hidden" name="minggu" value="${minggu}">
                                    <button class="btn btn-line btn-icon danger" title="Hapus"><i class="bi bi-trash3"></i></button>
                                </form>
                            </td>
                        </tr>
                    </c:forEach>
                    <c:if test="${empty riwayat}">
                        <tr><td colspan="4" class="empty"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada pembayaran minggu ini.</td></tr>
                    </c:if>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    // Isi nominal otomatis sesuai iuran anggota yang dipilih
    document.getElementById('anggotaId').addEventListener('change', function () {
        var opt = this.options[this.selectedIndex];
        if (opt.dataset.iuran) document.getElementById('jumlah').value = opt.dataset.iuran;
    });
</script>
<%@ include file="_footer.jsp" %>
