<div class="page-head">
    <div>
        <div class="eyebrow"><?= $bulan === '' ? 'Semua periode' : 'Periode bulan' ?></div>
        <h1>Rekap Kas</h1>
    </div>
    <form method="get" action="<?= site_url('kas') ?>" class="d-flex gap-2">
        <input type="month" name="bulan" class="form-control" value="<?= esc($bulan) ?>" onchange="this.form.submit()" aria-label="Pilih bulan">
        <?php if ($bulan !== ''): ?><a href="<?= site_url('kas') ?>" class="btn btn-line text-nowrap">Semua</a><?php endif ?>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="panel stat stat-ink">
            <div class="label"><i class="bi bi-safe"></i> Saldo kas</div>
            <div class="value"><?= rupiah($saldoTotal) ?></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="panel stat">
            <div class="label"><i class="bi bi-arrow-down-left"></i> Pemasukan</div>
            <div class="value"><?= rupiah($pemasukan) ?></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="panel stat">
            <div class="label"><i class="bi bi-arrow-up-right"></i> Pengeluaran</div>
            <div class="value"><?= rupiah($pengeluaran) ?></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="panel stat">
            <div class="label"><i class="bi bi-plus-slash-minus"></i> Selisih periode</div>
            <div class="value"><?= rupiah($saldo) ?></div>
        </div>
    </div>
</div>

<div class="panel mb-4">
    <div class="panel-head"><h2>Struk hari main</h2><span class="subtle"><?= count($hariMain) ?> hari</span></div>
    <div class="table-responsive">
        <table class="tbl">
            <tbody>
            <?php foreach ($hariMain as $s): ?>
                <tr>
                    <td>
                        <span class="fw-semibold text-nowrap"><?= esc(nama_tanggal($s['tanggal'])) ?></span>
                        <div class="subtle" style="font-size:12.5px"><?= count($s['hadir']) + $s['guest'] ?> orang main &middot; sisa <?= rupiah($s['masuk'] - $s['keluar']) ?></div>
                    </td>
                    <td class="num text-nowrap">
                        <a href="<?= site_url('kas/struk/' . tanggal($s['tanggal'])) ?>" class="btn btn-line btn-sm text-nowrap"><i class="bi bi-receipt me-1"></i> Struk</a>
                        <?php if ($isAdmin): ?>
                            <form method="post" action="<?= site_url('kas/struk/hapus/' . tanggal($s['tanggal'])) ?>" class="d-inline"
                                  onsubmit="return confirm('Hapus struk <?= esc(nama_tanggal($s['tanggal']), 'js') ?>?\n\nSemua catatan tanggal ini (yang main, guest, pengeluaran, kas minggu lalu) ikut terhapus dan tidak bisa dikembalikan.');">
                                <?= csrf_field() ?>
                                <button class="btn btn-line btn-sm btn-icon danger" title="Hapus struk"><i class="bi bi-trash3"></i></button>
                            </form>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php if (empty($hariMain)): ?>
                <tr><td colspan="2" class="empty">Belum ada hari main di periode ini.</td></tr>
            <?php endif ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="panel">
            <div class="panel-head"><h2>Transaksi</h2><span class="subtle"><?= count($transaksi) ?> catatan</span></div>
            <div class="table-responsive">
                <table class="tbl">
                    <thead><tr><th>Keterangan</th><th class="wide">Tanggal</th><th class="num">Jumlah</th></tr></thead>
                    <tbody>
                    <?php foreach ($transaksi as $t): ?>
                        <tr>
                            <td>
                                <div class="person">
                                    <span class="avatar <?= $t['pemasukan'] ? '' : 'avatar-line' ?>">
                                        <i class="bi <?= $t['pemasukan'] ? 'bi-arrow-down-left' : 'bi-arrow-up-right' ?>"></i>
                                    </span>
                                    <div>
                                        <?= esc($t['keterangan']) ?>
                                        <div class="subtle" style="font-size:12.5px"><?= $t['pemasukan'] ? 'Pemasukan' : 'Pengeluaran' ?><span class="d-sm-none"> &middot; <?= tanggal($t['tanggal']) ?></span></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-nowrap wide"><?= tanggal($t['tanggal']) ?></td>
                            <td class="num <?= $t['pemasukan'] ? 'amount-in' : 'amount-out' ?>">
                                <?= $t['pemasukan'] ? '+' : '&minus;' ?> <?= rupiah($t['jumlah']) ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    <?php if (empty($transaksi)): ?>
                        <tr><td colspan="3" class="empty"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada transaksi.</td></tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
