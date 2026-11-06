<div class="struk-aksi no-print">
    <a href="<?= site_url('kas?bulan=' . $tanggal->format('Y-m')) ?>" class="btn btn-line"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
    <button type="button" class="btn btn-line" onclick="window.print()" title="Cetak"><i class="bi bi-printer"></i><span class="d-none d-sm-inline ms-1">Cetak</span></button>
    <button type="button" class="btn btn-ink" id="unduhJpg"><i class="bi bi-image me-1"></i> JPG</button>
    <button type="button" class="btn btn-ink" id="unduhPdf"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</button>
    <?php if ($isAdmin): ?>
        <form method="post" action="<?= site_url('kas/struk/hapus/' . tanggal($tanggal)) ?>" class="d-flex"
              onsubmit="return confirm('Hapus struk <?= esc(nama_tanggal($tanggal), 'js') ?>?\n\nSemua catatan tanggal ini (yang main, guest, pengeluaran, kas minggu lalu) ikut terhapus dan tidak bisa dikembalikan.');">
            <?= csrf_field() ?>
            <button class="btn btn-line danger" title="Hapus struk"><i class="bi bi-trash3"></i></button>
        </form>
    <?php endif ?>
</div>

<article class="struk" aria-label="Struk kas <?= esc(nama_tanggal($tanggal)) ?>">
    <?php // Logo kok sebagai gambar supaya ikut tersimpan di JPG/PDF ?>
    <img class="struk-logo" alt="" src="data:image/svg+xml;base64,<?= base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="300" height="220" viewBox="0 0 300 220" fill="none" stroke="#000" stroke-width="5"> <g transform="rotate(-8 150 110) translate(0 220) rotate(-90)"> <ellipse cx="110" cy="38" rx="92" ry="18"/> <path d="M18 38 L72 212 M202 38 L148 212"/> <path d="M49 50 L86 212 M80 55 L99 212 M110 56 L110 212 M140 55 L121 212 M171 50 L134 212"/> <path d="M40 104 Q110 128 180 104 M58 162 Q110 180 162 162" stroke-width="4"/> <rect x="68" y="210" width="84" height="14" fill="#000"/> <path d="M68 224 Q68 280 110 280 Q152 280 152 224 Z" fill="#000"/> </g> </svg>') ?>">

    <div class="struk-klub"><?= esc(mb_strtoupper($klub)) ?></div>
    <div class="struk-sub">Uang Kas Badminton</div>

    <div class="struk-baris mt-4"><span><?= $tanggal->format('d-m-Y') ?></span><span><?= esc($dicetak) ?></span></div>
    <div class="struk-baris"><span>Transaksi</span><span><?= esc($nomor) ?></span></div>
    <div class="struk-baris"><span>Hari</span><span><?= esc(explode(',', nama_tanggal($tanggal))[0]) ?></span></div>

    <div class="struk-judul">Yang Main (<?= count($hadir) + $guest ?> orang)</div>
    <?php foreach ($hadir as $p): ?>
        <div class="struk-item"><?= esc($p->getAnggota()->getNama()) ?></div>
        <div class="struk-baris"><span>1 x <?= angka($p->getJumlah()) ?></span><span><?= angka($p->getJumlah()) ?></span></div>
    <?php endforeach ?>
    <?php if ($guest > 0): ?>
        <div class="struk-item">Guest</div>
        <div class="struk-baris"><span><?= $guest ?> x <?= angka($tarifGuest) ?></span><span><?= angka($guest * $tarifGuest) ?></span></div>
    <?php endif ?>
    <?php if ($hadir === [] && $guest === 0): ?>
        <div class="struk-baris"><span>Tidak ada</span><span>0</span></div>
    <?php endif ?>

    <div class="struk-baris mt-3"><span>Subtotal masuk</span><span><?= angka($masuk) ?></span></div>

    <?php if ($pengeluaranHari !== []): ?>
        <div class="struk-judul">Pengeluaran</div>
        <?php foreach ($pengeluaranHari as $p): ?>
            <div class="struk-baris">
                <span><?= esc($p->getKategori()) ?><?= $p->getKeterangan() ? ' <small>(' . esc($p->getKeterangan()) . ')</small>' : '' ?></span>
                <span>-<?= angka($p->getJumlah()) ?></span>
            </div>
        <?php endforeach ?>
        <div class="struk-baris mt-3"><span>Subtotal keluar</span><span>-<?= angka($keluarHari) ?></span></div>
    <?php endif ?>

    <div class="struk-garis"></div>
    <?php if ($kasLalu > 0 || $dariKas !== []): ?>
        <div class="struk-baris mt-3"><span>Kas minggu lalu</span><span><?= angka($kasLalu) ?></span></div>
        <?php foreach ($dariKas as $p): ?>
            <div class="struk-baris"><span><?= esc($p->getKategori()) ?></span><span>-<?= angka($p->getJumlah()) ?></span></div>
        <?php endforeach ?>
        <div class="struk-baris"><span>Sisa kas minggu lalu</span><span><?= angka($kasLalu - $keluarKas) ?></span></div>

        <div class="struk-garis"></div>
        <div class="struk-baris"><span>Sisa hari ini</span><span><?= angka($masuk - $keluarHari) ?></span></div>
        <div class="struk-baris"><span>Sisa kas minggu lalu</span><span><?= angka($kasLalu - $keluarKas) ?></span></div>
        <div class="struk-garis"></div>
        <div class="struk-baris struk-total"><span>Total kas</span><span><?= angka($kasLalu + $masuk - $keluar) ?></span></div>
    <?php else: ?>
        <div class="struk-baris struk-total"><span>Sisa</span><span><?= angka($masuk - $keluar) ?></span></div>
    <?php endif ?>

    <div class="struk-tutup">
        Terima kasih
    </div>
</article>

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script>
    (function () {
        var nama = 'struk-<?= tanggal($tanggal) ?>';
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
