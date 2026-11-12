<%@ include file="_header.jsp" %>
<div class="struk-aksi no-print">
    <a href="${ctx}/kas?bulan=${fn:substring(tanggal, 0, 7)}" class="btn btn-line"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
    <button type="button" class="btn btn-line" onclick="window.print()" title="Cetak"><i class="bi bi-printer"></i><span class="d-none d-sm-inline ms-1">Cetak</span></button>
    <button type="button" class="btn btn-ink" id="unduhJpg"><i class="bi bi-image me-1"></i> JPG</button>
    <button type="button" class="btn btn-ink" id="unduhPdf"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</button>
    <c:if test="${isAdmin}">
        <form method="post" action="${ctx}/kas/struk" class="d-flex"
              onsubmit="return confirm('Hapus struk ${f:namaTanggal(tanggal)}?\n\nSemua catatan tanggal ini (yang main, guest, pengeluaran, kas minggu lalu) ikut terhapus dan tidak bisa dikembalikan.');">
            <input type="hidden" name="aksi" value="hapus">
            <input type="hidden" name="tanggal" value="${tanggal}">
            <button class="btn btn-line danger" title="Hapus struk"><i class="bi bi-trash3"></i></button>
        </form>
    </c:if>
</div>

<article class="struk" aria-label="Struk kas ${f:namaTanggal(tanggal)}">
    <%-- Logo kok sebagai gambar supaya ikut tersimpan di JPG/PDF --%>
    <img class="struk-logo" alt="" src="${ctx}/assets/kok.svg">

    <div class="struk-klub"><c:out value="${fn:toUpperCase(klub)}"/></div>
    <div class="struk-sub">Uang Kas Badminton</div>

    <div class="struk-baris mt-4"><span>${tanggalTeks}</span><span>${dicetak}</span></div>
    <div class="struk-baris"><span>Transaksi</span><span>${nomor}</span></div>
    <div class="struk-baris"><span>Hari</span><span>${f:namaHari(tanggal)}</span></div>

    <div class="struk-judul">Yang Main (${fn:length(hadir) + guest} orang)</div>
    <%-- Satu baris per orang supaya struk tidak kepanjangan --%>
    <c:forEach var="p" items="${hadir}">
        <div class="struk-baris">
            <span><c:out value="${p.anggota.nama}"/><c:if test="${p.potongTalangan > 0}"> <small>(talangan${p.jumlah > 0 ? ' -'.concat(f:angka(p.potongTalangan)) : ''})</small></c:if></span>
            <span>${f:angka(p.jumlah)}</span>
        </div>
    </c:forEach>
    <c:if test="${guest > 0}">
        <div class="struk-baris"><span>Guest <small>${guest} x ${f:angka(tarifGuest)}</small></span><span>${f:angka(guest * tarifGuest)}</span></div>
    </c:if>
    <c:if test="${empty hadir and guest == 0}">
        <div class="struk-baris"><span>Tidak ada</span><span>0</span></div>
    </c:if>

    <div class="struk-baris mt-3"><span>Subtotal masuk</span><span>${f:angka(masuk)}</span></div>

    <c:if test="${not empty pengeluaranHari}">
        <div class="struk-judul">Pengeluaran</div>
        <c:forEach var="p" items="${pengeluaranHari}">
            <div class="struk-baris">
                <span><c:out value="${p.kategori}"/><c:if test="${not empty p.keterangan}"> <small>(<c:out value="${p.keterangan}"/>)</small></c:if></span>
                <span>-${f:angka(p.jumlah)}</span>
            </div>
        </c:forEach>
        <div class="struk-baris mt-3"><span>Subtotal keluar</span><span>-${f:angka(keluarHari)}</span></div>
    </c:if>

    <div class="struk-garis"></div>
    <c:choose>
        <c:when test="${kasLalu > 0 or not empty dariKas}">
            <div class="struk-baris mt-3"><span>Kas minggu lalu</span><span>${f:angka(kasLalu)}</span></div>
            <c:forEach var="p" items="${dariKas}">
                <div class="struk-baris"><span><c:out value="${p.kategori}"/></span><span>-${f:angka(p.jumlah)}</span></div>
            </c:forEach>
            <div class="struk-baris"><span>Sisa kas minggu lalu</span><span>${f:angka(kasLalu - keluarKas)}</span></div>

            <div class="struk-garis"></div>
            <div class="struk-baris"><span>Sisa hari ini</span><span>${f:angka(masuk - keluarHari)}</span></div>
            <div class="struk-baris"><span>Sisa kas minggu lalu</span><span>${f:angka(kasLalu - keluarKas)}</span></div>
            <div class="struk-garis"></div>
            <div class="struk-baris struk-total"><span>Total kas</span><span>${f:angka(kasLalu + masuk - keluar)}</span></div>
        </c:when>
        <c:otherwise>
            <div class="struk-baris struk-total"><span>Sisa</span><span>${f:angka(masuk - keluar)}</span></div>
        </c:otherwise>
    </c:choose>

    <c:if test="${not empty talangan}">
        <div class="struk-judul">Sisa Talangan</div>
        <c:forEach var="t" items="${talangan}">
            <div class="struk-baris"><span><c:out value="${t.key}"/></span><span>${t.value > 0 ? f:angka(t.value) : 'Lunas'}</span></div>
        </c:forEach>
    </c:if>

    <div class="struk-tutup">
        Terima kasih
    </div>
</article>

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script>
    (function () {
        var nama = 'struk-${tanggal}';
        var struk = document.querySelector('.struk');

        function gambar() {
            // scale 3 supaya hasilnya tajam waktu di-zoom di HP
            return html2canvas(struk, { scale: 3, backgroundColor: '#ffffff', useCORS: true });
        }
        function unduh(url, file) {
            var a = document.createElement('a');
            a.href = url; a.download = file;
            document.body.appendChild(a); a.click(); a.remove();
        }
        function pakaiTombol(id, kerja) {
            var b = document.getElementById(id);
            b.addEventListener('click', function () {
                var asli = b.innerHTML;
                b.disabled = true; b.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                Promise.resolve().then(kerja).catch(function () {
                    alert('Gagal membuat file. Coba lagi, atau pakai tombol Cetak.');
                }).then(function () { b.disabled = false; b.innerHTML = asli; });
            });
        }

        pakaiTombol('unduhJpg', function () {
            return gambar().then(function (c) { unduh(c.toDataURL('image/jpeg', 0.92), nama + '.jpg'); });
        });
        pakaiTombol('unduhPdf', function () {
            return gambar().then(function (c) {
                // Ukuran kertas struk: lebar 80 mm, tinggi mengikuti isi
                var lebar = 80, tinggi = c.height * lebar / c.width;
                var pdf = new window.jspdf.jsPDF({ unit: 'mm', format: [lebar, tinggi], orientation: 'portrait' });
                pdf.addImage(c.toDataURL('image/jpeg', 0.92), 'JPEG', 0, 0, lebar, tinggi);
                pdf.save(nama + '.pdf');
            });
        });
    })();
</script>
<%@ include file="_footer.jsp" %>
