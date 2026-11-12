<%@ include file="_header.jsp" %>
<div class="page-head">
    <div>
        <div class="eyebrow">${f:namaTanggal(tanggal)}</div>
        <h1>Catat Main</h1>
    </div>
    <form method="get" action="${ctx}/pembayaran">
        <input type="date" name="tanggal" class="form-control" value="${tanggal}" onchange="this.form.submit()" aria-label="Pilih tanggal main">
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="panel stat stat-ink">
            <div class="label"><i class="bi bi-people"></i> Yang main</div>
            <div class="value">${fn:length(hadir) + guest} <span class="fs-6 fw-normal text-secondary">orang</span></div>
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
            <div class="label"><i class="bi bi-plus-slash-minus"></i> Sisa hari ini</div>
            <div class="value">${f:rupiah(sisaHari)}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="panel">
            <div class="panel-head">
                <h2>Yang datang</h2>
                <c:if test="${isAdmin and not empty daftarAnggota}">
                    <button type="button" class="btn btn-line btn-sm" id="centangSemua">Centang semua</button>
                </c:if>
            </div>
            <c:choose>
                <c:when test="${isAdmin}">
                    <form class="panel-body" method="post" action="${ctx}/pembayaran" id="formHadir">
                        <input type="hidden" name="tanggal" value="${tanggal}">
                        <p class="subtle mb-3">Centang anggota yang main hari ini. Yang dicentang otomatis bayar sesuai iurannya.
                            Ada anggota baru? <a href="${ctx}/anggota?aksi=tambah" class="fw-semibold">Tambah anggota</a> &middot;
                            <a href="${ctx}/anggota" class="fw-semibold">Edit / hapus</a></p>
                        <div class="hadir-list mb-4">
                            <c:forEach var="a" items="${daftarAnggota}">
                                <c:set var="sudah" value="${hadirIds.contains(a.id)}"/>
                                <label class="hadir-item">
                                    <input type="checkbox" name="hadir" value="${a.id}" ${sudah ? 'checked data-awal="1"' : ''}>
                                    <span class="cek"><i class="bi bi-check-lg"></i></span>
                                    <span class="nama"><c:out value="${a.nama}"/><small>${f:rupiah(a.nominalIuran)}<c:if test="${sisaTalangan[a.id] > 0}"> &middot; talangan ${f:angka(sisaTalangan[a.id])}</c:if></small></span>
                                </label>
                            </c:forEach>
                            <c:if test="${not empty daftarAnggota}">
                                <div class="guest-item${guest > 0 ? ' aktif' : ''}" id="guestItem">
                                    <span class="cek"><i class="bi bi-person-plus"></i></span>
                                    <span class="nama">Guest
                                        <small>
                                            <label for="tarifGuest">Rp</label>
                                            <input type="text" name="tarifGuest" id="tarifGuest" class="guest-tarif input-rupiah" inputmode="numeric"
                                                   value="${f:angka(tarifGuest)}" aria-label="Bayar per guest">/orang
                                            <span>= <b id="totalGuest">${f:rupiah(guest * tarifGuest)}</b></span>
                                        </small>
                                    </span>
                                    <span class="guest-stepper" role="group" aria-label="Jumlah guest">
                                        <button type="button" class="btn btn-line" data-guest="-1" aria-label="Kurangi guest">&minus;</button>
                                        <input type="number" name="guest" id="guest" class="form-control" min="0" max="99" inputmode="numeric"
                                               value="${guest}" aria-label="Jumlah guest">
                                        <button type="button" class="btn btn-line" data-guest="1" aria-label="Tambah guest">+</button>
                                    </span>
                                </div>
                            </c:if>
                            <c:if test="${empty daftarAnggota}">
                                <p class="subtle mb-0">Belum ada anggota. Tambahkan dulu di menu Anggota.</p>
                            </c:if>
                        </div>
                        <button class="btn btn-ink w-100">Simpan yang datang</button>
                    </form>
                </c:when>
                <c:otherwise>
                    <table class="tbl">
                        <tbody>
                        <c:forEach var="p" items="${hadir}">
                            <tr>
                                <td>
                                    <div class="person">
                                        <span class="avatar">${f:inisial(p.anggota.nama)}</span>
                                        <c:out value="${p.anggota.nama}"/>
                                    </div>
                                </td>
                                <td class="num amount-in">${f:rupiah(p.jumlah)}<c:if test="${p.potongTalangan > 0}"><div class="subtle" style="font-size:11.5px">potong talangan ${f:angka(p.potongTalangan)}</div></c:if></td>
                            </tr>
                        </c:forEach>
                        <c:if test="${guest > 0}">
                            <tr>
                                <td>
                                    <div class="person">
                                        <span class="avatar avatar-line"><i class="bi bi-person-plus"></i></span>
                                        Guest ${guest} orang <span class="subtle">(${guest} &times; ${f:angka(tarifGuest)})</span>
                                    </div>
                                </td>
                                <td class="num amount-in">${f:rupiah(guest * tarifGuest)}</td>
                            </tr>
                        </c:if>
                        <c:if test="${empty hadir and guest == 0}">
                            <tr><td colspan="2" class="empty"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada yang dicatat main di tanggal ini.</td></tr>
                        </c:if>
                        </tbody>
                    </table>
                </c:otherwise>
            </c:choose>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="panel mb-4">
            <div class="panel-head"><h2>Kas minggu lalu</h2></div>
            <div class="panel-body">
                <c:if test="${isAdmin}">
                    <form method="post" action="${ctx}/pembayaran" class="mb-3">
                        <input type="hidden" name="aksi" value="kasLalu">
                        <input type="hidden" name="tanggal" value="${tanggal}">
                        <label class="form-label" for="kasLalu">Sisa kas yang dibawa dari minggu lalu</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="text" id="kasLalu" name="kasLalu" class="form-control input-rupiah" inputmode="numeric"
                                   placeholder="0" value="${kasLalu > 0 ? f:angka(kasLalu) : ''}">
                        </div>
                        <c:if test="${kasSebelumTanggal != null}">
                            <div class="subtle mt-2" style="font-size:12.5px">
                                Total kas main sebelumnya (${f:namaTanggal(kasSebelumTanggal)}):
                                <a href="#" class="fw-semibold" onclick="var k=document.getElementById('kasLalu');k.value='${f:angka(kasSebelumTotal > 0 ? kasSebelumTotal : 0)}';k.focus();return false;">${f:rupiah(kasSebelumTotal)}</a>
                                &middot; klik angkanya untuk memakai.
                            </div>
                        </c:if>
                        <p class="subtle mt-3 mb-2">Diambil dari kas minggu lalu:</p>
                        <c:forEach var="b" items="${barisKasLalu}">
                            <%@ include file="_baris-keluar.jspf" %>
                        </c:forEach>
                        <button class="btn btn-ink w-100 mt-2">Simpan Kas Minggu Lalu</button>
                    </form>
                </c:if>
                <div class="kas-hitung">
                    <div><span>Kas minggu lalu</span><span>${f:rupiah(kasLalu)}</span></div>
                    <c:forEach var="p" items="${dariKas}">
                        <div class="kas-kurang"><span>&nbsp;&nbsp;&minus; <c:out value="${p.kategori}"/></span><span>&minus; ${f:rupiah(p.jumlah)}</span></div>
                    </c:forEach>
                    <c:if test="${not empty dariKas}">
                        <div><span>Sisa kas minggu lalu</span><span>${f:rupiah(kasLalu - keluarKas)}</span></div>
                    </c:if>
                    <div><span>Sisa hari ini</span><span>${sisaHari < 0 ? '&minus; ' : '+ '}${f:rupiah(sisaHari < 0 ? -sisaHari : sisaHari)}</span></div>
                    <div class="kas-total"><span>Total kas</span><span>${f:rupiah(totalKas)}</span></div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><h2>Pengeluaran</h2></div>
            <c:choose>
                <c:when test="${isAdmin}">
                    <form class="panel-body" method="post" action="${ctx}/pembayaran">
                        <input type="hidden" name="aksi" value="pengeluaran">
                        <input type="hidden" name="tanggal" value="${tanggal}">
                        <p class="subtle mb-3">Dibayar pakai uang hari ini. Kosongkan kalau tidak ada; kosongkan juga untuk menghapus.</p>
                        <c:forEach var="b" items="${barisHariIni}">
                            <%@ include file="_baris-keluar.jspf" %>
                        </c:forEach>
                        <button class="btn btn-ink w-100 mt-2">Simpan Pengeluaran</button>
                    </form>
                </c:when>
                <c:otherwise>
                    <table class="tbl">
                        <tbody>
                        <c:forEach var="p" items="${pengeluaran}">
                            <tr>
                                <td>
                                    <c:out value="${p.kategori}"/>
                                    <c:if test="${not empty p.keterangan}"><div class="subtle" style="font-size:12.5px"><c:out value="${p.keterangan}"/></div></c:if>
                                </td>
                                <td class="num amount-out">&minus; ${f:rupiah(p.jumlah)}</td>
                            </tr>
                        </c:forEach>
                        <c:if test="${empty pengeluaran}">
                            <tr><td colspan="2" class="empty">Belum ada pengeluaran di tanggal ini.</td></tr>
                        </c:if>
                        </tbody>
                    </table>
                </c:otherwise>
            </c:choose>
        </div>
    </div>
</div>
<c:if test="${isAdmin}">
<script>
    (function () {
        var form = document.getElementById('formHadir');
        var kotak = form ? form.querySelectorAll('input[name="hadir"]') : [];
        var tombol = document.getElementById('centangSemua');
        if (tombol) tombol.addEventListener('click', function () {
            var semua = Array.prototype.every.call(kotak, function (k) { return k.checked; });
            kotak.forEach(function (k) { k.checked = !semua; });
            tombol.textContent = semua ? 'Centang semua' : 'Lepas semua';
        });
        // Guest: tombol - / + dan total otomatis (orang x tarif)
        var guest = document.getElementById('guest'), tarif = document.getElementById('tarifGuest'), total = document.getElementById('totalGuest');
        function hitungGuest() {
            var n = Math.max(0, parseInt(guest.value, 10) || 0);
            var t = parseInt((tarif.value || '').replace(/\D/g, ''), 10) || 0;
            total.textContent = 'Rp ' + (n * t).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            document.getElementById('guestItem').classList.toggle('aktif', n > 0);
        }
        if (guest) {
            document.querySelectorAll('[data-guest]').forEach(function (b) {
                b.addEventListener('click', function () {
                    guest.value = Math.min(99, Math.max(0, (parseInt(guest.value, 10) || 0) + parseInt(b.dataset.guest, 10)));
                    hitungGuest();
                });
            });
            guest.addEventListener('input', hitungGuest);
            tarif.addEventListener('input', hitungGuest);
        }
        // Konfirmasi kalau ada yang sebelumnya tercatat lalu dilepas centangnya
        if (form) form.addEventListener('submit', function (e) {
            var dilepas = form.querySelectorAll('input[data-awal]:not(:checked)').length;
            if (dilepas > 0 && !confirm(dilepas + ' orang yang sudah tercatat akan dihapus dari tanggal ini. Lanjut?')) e.preventDefault();
        });
    })();
</script>
</c:if>
<%@ include file="_footer.jsp" %>
