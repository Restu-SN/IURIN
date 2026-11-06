<div class="page-head">
    <div>
        <div class="eyebrow"><?= esc($namaBulan) ?></div>
        <h1>Riwayat Main</h1>
    </div>
    <form method="get" action="<?= site_url('status') ?>">
        <input type="month" name="bulan" class="form-control" value="<?= esc($bulan) ?>" onchange="this.form.submit()" aria-label="Pilih bulan">
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="panel stat stat-ink">
            <div class="label"><i class="bi bi-calendar-check"></i> Hari main</div>
            <div class="value"><?= $hariMain ?> <span class="fs-6 fw-normal text-secondary">kali</span></div>
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
            <div class="label"><i class="bi bi-plus-slash-minus"></i> Sisa bulan ini</div>
            <div class="value"><?= rupiah($masuk - $keluar) ?></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="panel">
            <div class="panel-head"><h2>Per tanggal</h2><span class="subtle"><?= count($sesi) ?> hari</span></div>
            <div class="table-responsive">
                <table class="tbl">
                    <thead><tr><th>Tanggal</th><th class="num">Masuk</th><th class="num">Keluar</th><th class="num">Sisa</th></tr></thead>
                    <tbody>
                    <?php foreach ($sesi as $s): ?>
                        <tr>
                            <td>
                                <a href="<?= site_url('pembayaran?tanggal=' . tanggal($s['tanggal'])) ?>" class="fw-semibold text-nowrap"><?= esc(nama_tanggal($s['tanggal'])) ?></a>
                                <div class="hadir-nama">
                                    <?= count($s['hadir']) ?> orang<?= $s['hadir'] !== [] ? ': ' . esc(implode(', ', $s['hadir'])) : '' ?><?= $s['guest'] > 0 ? ' + ' . $s['guest'] . ' guest' : '' ?>
                                    <?php if ($s['pengeluaran'] !== []): ?><br>Keluar: <?= esc(implode(', ', $s['pengeluaran'])) ?><?php endif ?>
                                </div>
                            </td>
                            <td class="num amount-in text-nowrap"><?= rupiah($s['masuk']) ?></td>
                            <td class="num amount-out text-nowrap"><?= $s['keluar'] > 0 ? '&minus; ' . rupiah($s['keluar']) : '<span class="subtle">&mdash;</span>' ?></td>
                            <td class="num text-nowrap fw-bold"><?= rupiah($s['masuk'] - $s['keluar']) ?></td>
                        </tr>
                    <?php endforeach ?>
                    <?php if (empty($sesi)): ?>
                        <tr><td colspan="4" class="empty"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada catatan main bulan ini.</td></tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="panel">
            <div class="panel-head"><h2>Per anggota</h2><span class="subtle"><?= esc($namaBulan) ?></span></div>
            <div class="table-responsive">
                <table class="tbl">
                    <thead><tr><th>Nama</th><th class="num">Main</th><th class="num">Bayar</th></tr></thead>
                    <tbody>
                    <?php foreach ($anggota as $r): ?>
                        <tr>
                            <td>
                                <div class="person">
                                    <span class="avatar <?= $r['main'] > 0 ? '' : 'avatar-line' ?>"><?= esc(inisial($r['anggota']->getNama())) ?></span>
                                    <div>
                                        <?= esc($r['anggota']->getNama()) ?>
                                        <div class="subtle" style="font-size:12.5px">Terakhir: <?= $r['terakhir'] ? esc(nama_tanggal(new DateTimeImmutable($r['terakhir']))) : 'belum pernah' ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="num fw-bold"><?= $r['main'] ?>&times;</td>
                            <td class="num text-nowrap"><?= $r['total'] > 0 ? rupiah($r['total']) : '<span class="subtle">&mdash;</span>' ?></td>
                        </tr>
                    <?php endforeach ?>
                    <?php if (empty($anggota)): ?>
                        <tr><td colspan="3" class="empty">Belum ada anggota.</td></tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
