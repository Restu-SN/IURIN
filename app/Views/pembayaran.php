<?php
$tgl = tanggal($tanggal);
// Nominal & catatan yang sudah tercatat per jenis, untuk diisi ulang ke kolomnya
$isi = [];
foreach ($pengeluaran as $p) {
    $kunci = \App\Entities\Pengeluaran::kunci($p->getKategori());
    $isi[$kunci]['jumlah'] = ($isi[$kunci]['jumlah'] ?? 0) + $p->getJumlah();
    $isi[$kunci]['ket'] ??= $p->getKeterangan();
}
$ikon = ['Beli Air' => 'bi-droplet', 'Uang Parkir' => 'bi-p-square', 'Pembelian Kok' => 'bi-bag', 'Lainnya' => 'bi-three-dots'];
// Satu baris isian pengeluaran (nama, kolom Rp, catatan khusus Lainnya)
$barisKeluar = static function (int $i, string $k) use ($isi, $ikon): string {
    $d = $isi[\App\Entities\Pengeluaran::kunci($k)] ?? [];
    $j = $d['jumlah'] ?? 0;
    $html = '<div class="keluar-baris"><label class="keluar-nama" for="jml' . $i . '"><i class="bi ' . ($ikon[$k] ?? 'bi-dot') . '"></i> ' . esc($k) . '</label>'
        . '<div class="input-group"><span class="input-group-text bg-white">Rp</span>'
        . '<input type="text" id="jml' . $i . '" name="jumlah[' . $i . ']" class="form-control input-rupiah" inputmode="numeric" placeholder="0" value="' . ($j > 0 ? angka($j) : '') . '"></div>';
    if ($k === 'Lainnya') {
        $html .= '<input type="text" name="keterangan[' . $i . ']" class="form-control mt-2" maxlength="255" placeholder="Untuk apa? (mis. plester, kok rusak)" value="' . esc($d['ket'] ?? '') . '" aria-label="Catatan pengeluaran lainnya">';
    }

    return $html . '</div>';
};
?>
<div class="page-head">
    <div>
        <div class="eyebrow"><?= esc(nama_tanggal($tanggal)) ?></div>
        <h1>Catat Main</h1>
    </div>
    <form method="get" action="<?= site_url('pembayaran') ?>">
        <input type="date" name="tanggal" class="form-control" value="<?= $tgl ?>" onchange="this.form.submit()" aria-label="Pilih tanggal main">
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="panel stat stat-ink">
            <div class="label"><i class="bi bi-people"></i> Yang main</div>
            <div class="value"><?= count($hadir) + $guest ?> <span class="fs-6 fw-normal text-secondary">orang</span></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="panel stat">
            <div class="label"><i class="bi bi-arrow-down-left"></i> Uang masuk</div>
            <div class="value"><?= rupiah($masuk) ?></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="panel stat">
            <div class="label"><i class="bi bi-arrow-up-right"></i> Pengeluaran</div>
            <div class="value"><?= rupiah($keluar) ?></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="panel stat">
            <div class="label"><i class="bi bi-plus-slash-minus"></i> Sisa hari ini</div>
            <div class="value"><?= rupiah($masuk - ($keluar - $keluarKas)) ?></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="panel">
            <div class="panel-head">
                <h2>Yang datang</h2>
                <?php if ($isAdmin && ! empty($daftarAnggota)): ?>
                    <button type="button" class="btn btn-line btn-sm" id="centangSemua">Centang semua</button>
                <?php endif ?>
            </div>
            <?php if ($isAdmin): ?>
                <form class="panel-body" method="post" action="<?= site_url('pembayaran') ?>" id="formHadir">
                    <?= csrf_field() ?>
                    <input type="hidden" name="tanggal" value="<?= $tgl ?>">
                    <p class="subtle mb-3">Centang anggota yang main hari ini. Yang dicentang otomatis bayar sesuai iurannya.
                        Ada anggota baru? <a href="<?= site_url('anggota/tambah') ?>" class="fw-semibold">Tambah anggota</a> &middot;
                        <a href="<?= site_url('anggota') ?>" class="fw-semibold">Edit / hapus</a></p>
                    <div class="hadir-list mb-4">
                        <?php foreach ($daftarAnggota as $a): $sudah = in_array($a->getId(), $hadirIds, true); ?>
                            <label class="hadir-item">
                                <input type="checkbox" name="hadir[]" value="<?= $a->getId() ?>" <?= $sudah ? 'checked data-awal="1"' : '' ?>>
                                <span class="cek"><i class="bi bi-check-lg"></i></span>
                                <span class="nama"><?= esc($a->getNama()) ?><small><?= rupiah($a->getNominalIuran()) ?><?php if (($sisaTalangan[$a->getId()] ?? 0) > 0): ?> &middot; talangan <?= angka($sisaTalangan[$a->getId()]) ?><?php endif ?></small></span>
                            </label>
                        <?php endforeach ?>
                        <?php if (! empty($daftarAnggota)): ?>
                            <div class="guest-item<?= $guest > 0 ? ' aktif' : '' ?>" id="guestItem">
                                <span class="cek"><i class="bi bi-person-plus"></i></span>
                                <span class="nama">Guest
                                    <small>
                                        <label for="tarifGuest">Rp</label>
                                        <input type="text" name="tarifGuest" id="tarifGuest" class="guest-tarif input-rupiah" inputmode="numeric"
                                               value="<?= angka($tarifGuest) ?>" aria-label="Bayar per guest">/orang
                                        <span>= <b id="totalGuest"><?= rupiah($guest * $tarifGuest) ?></b></span>
                                    </small>
                                </span>
                                <span class="guest-stepper" role="group" aria-label="Jumlah guest">
                                    <button type="button" class="btn btn-line" data-guest="-1" aria-label="Kurangi guest">&minus;</button>
                                    <input type="number" name="guest" id="guest" class="form-control" min="0" max="99" inputmode="numeric"
                                           value="<?= $guest ?>" aria-label="Jumlah guest">
                                    <button type="button" class="btn btn-line" data-guest="1" aria-label="Tambah guest">+</button>
                                </span>
                            </div>
                        <?php endif ?>
                        <?php if (empty($daftarAnggota)): ?>
                            <p class="subtle mb-0">Belum ada anggota. Tambahkan dulu di menu Anggota.</p>
                        <?php endif ?>
                    </div>
                    <button class="btn btn-ink w-100">Simpan yang datang</button>
                </form>
            <?php else: ?>
                <table class="tbl">
                    <tbody>
                    <?php foreach ($hadir as $p): ?>
                        <tr>
                            <td>
                                <div class="person">
                                    <span class="avatar"><?= esc(inisial($p->getAnggota()->getNama())) ?></span>
                                    <?= esc($p->getAnggota()->getNama()) ?>
                                </div>
                            </td>
                            <td class="num amount-in"><?= rupiah($p->getJumlah()) ?><?php if ($p->getPotongTalangan() > 0): ?><div class="subtle" style="font-size:11.5px">potong talangan <?= angka($p->getPotongTalangan()) ?></div><?php endif ?></td>
                        </tr>
                    <?php endforeach ?>
                    <?php if ($guest > 0): ?>
                        <tr>
                            <td>
                                <div class="person">
                                    <span class="avatar avatar-line"><i class="bi bi-person-plus"></i></span>
                                    Guest <?= $guest ?> orang <span class="subtle">(<?= $guest ?> &times; <?= angka($tarifGuest) ?>)</span>
                                </div>
                            </td>
                            <td class="num amount-in"><?= rupiah($guest * $tarifGuest) ?></td>
                        </tr>
                    <?php endif ?>
                    <?php if (empty($hadir) && $guest === 0): ?>
                        <tr><td colspan="2" class="empty"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada yang dicatat main di tanggal ini.</td></tr>
                    <?php endif ?>
                    </tbody>
                </table>
            <?php endif ?>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="panel mb-4">
            <div class="panel-head"><h2>Kas minggu lalu</h2></div>
            <div class="panel-body">
                <?php if ($isAdmin): ?>
                    <form method="post" action="<?= site_url('pembayaran/kas-lalu') ?>" class="mb-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="tanggal" value="<?= $tgl ?>">
                        <label class="form-label" for="kasLalu">Sisa kas yang dibawa dari minggu lalu</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="text" id="kasLalu" name="kasLalu" class="form-control input-rupiah" inputmode="numeric"
                                   placeholder="0" value="<?= $kasLalu > 0 ? angka($kasLalu) : '' ?>">
                        </div>
                        <?php if ($kasSebelum !== null): ?>
                            <div class="subtle mt-2" style="font-size:12.5px">
                                Total kas main sebelumnya (<?= esc(nama_tanggal($kasSebelum['tanggal'])) ?>):
                                <a href="#" class="fw-semibold" onclick="var k=document.getElementById('kasLalu');k.value='<?= angka(max(0, $kasSebelum['total'])) ?>';k.focus();return false;"><?= rupiah($kasSebelum['total']) ?></a>
                                &middot; klik angkanya untuk memakai.
                            </div>
                        <?php endif ?>
                        <p class="subtle mt-3 mb-2">Diambil dari kas minggu lalu:</p>
                        <?php foreach ($kategori as $i => $k): ?>
                            <?php if (\App\Entities\Pengeluaran::dariKasLalu($k)): ?><?= $barisKeluar($i, $k) ?><?php endif ?>
                        <?php endforeach ?>
                        <button class="btn btn-ink w-100 mt-2">Simpan Kas Minggu Lalu</button>
                    </form>
                <?php endif ?>
                <div class="kas-hitung">
                    <?php $sisaHari = $masuk - ($keluar - $keluarKas); ?>
                    <div><span>Kas minggu lalu</span><span><?= rupiah($kasLalu) ?></span></div>
                    <?php foreach ($dariKas as $p): ?>
                        <div class="kas-kurang"><span>&nbsp;&nbsp;&minus; <?= esc($p->getKategori()) ?></span><span>&minus; <?= rupiah($p->getJumlah()) ?></span></div>
                    <?php endforeach ?>
                    <?php if ($dariKas !== []): ?>
                        <div><span>Sisa kas minggu lalu</span><span><?= rupiah($kasLalu - $keluarKas) ?></span></div>
                    <?php endif ?>
                    <div><span>Sisa hari ini</span><span><?= $sisaHari < 0 ? '&minus; ' : '+ ' ?><?= rupiah(abs($sisaHari)) ?></span></div>
                    <div class="kas-total"><span>Total kas</span><span><?= rupiah($kasLalu + $masuk - $keluar) ?></span></div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><h2>Pengeluaran</h2></div>
            <?php if ($isAdmin): ?>
                <form class="panel-body" method="post" action="<?= site_url('pembayaran/pengeluaran') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="tanggal" value="<?= $tgl ?>">
                    <p class="subtle mb-3">Dibayar pakai uang hari ini. Kosongkan kalau tidak ada; kosongkan juga untuk menghapus.</p>
                    <?php foreach ($kategori as $i => $k): ?>
                        <?php if (! \App\Entities\Pengeluaran::dariKasLalu($k)): ?><?= $barisKeluar($i, $k) ?><?php endif ?>
                    <?php endforeach ?>
                    <button class="btn btn-ink w-100 mt-2">Simpan Pengeluaran</button>
                </form>
            <?php else: ?>
                <table class="tbl">
                    <tbody>
                    <?php foreach ($pengeluaran as $p): ?>
                        <tr>
                            <td>
                                <?= esc($p->getKategori()) ?>
                                <?php if ($p->getKeterangan()): ?><div class="subtle" style="font-size:12.5px"><?= esc($p->getKeterangan()) ?></div><?php endif ?>
                            </td>
                            <td class="num amount-out">&minus; <?= rupiah($p->getJumlah()) ?></td>
                        </tr>
                    <?php endforeach ?>
                    <?php if (empty($pengeluaran)): ?>
                        <tr><td colspan="2" class="empty">Belum ada pengeluaran di tanggal ini.</td></tr>
                    <?php endif ?>
                    </tbody>
                </table>
            <?php endif ?>
        </div>
    </div>
</div>
<?php if ($isAdmin): ?>
<script>
    (function () {
        var form = document.getElementById('formHadir');
        var kotak = form ? form.querySelectorAll('input[name="hadir[]"]') : [];
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
<?php endif ?>
