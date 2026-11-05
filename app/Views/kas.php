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

<div class="row g-4">
    <div class="<?= $isAdmin ? 'col-xl-8 order-2 order-xl-1' : 'col-12' ?>">
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

    <?php if ($isAdmin): ?>
    <div class="col-xl-4 order-1 order-xl-2">
        <div class="panel">
            <div class="panel-head"><h2>Catat pengeluaran</h2></div>
            <form class="panel-body" method="post" action="<?= site_url('kas') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="filterBulan" value="<?= esc($bulan) ?>">
                <div class="mb-3">
                    <label class="form-label" for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" value="<?= $hariIni ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="kategori">Kategori</label>
                    <input type="text" id="kategori" name="kategori" class="form-control" list="kategoriList" required maxlength="50"
                           placeholder="Sewa Lapangan">
                    <datalist id="kategoriList">
                        <option value="Sewa Lapangan"><option value="Pembelian Kok"><option value="Konsumsi"><option value="Lainnya">
                    </datalist>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="ket">Keterangan</label>
                    <input type="text" id="ket" name="keterangan" class="form-control" maxlength="255">
                </div>
                <div class="mb-4">
                    <label class="form-label" for="jml">Nominal</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">Rp</span>
                        <input type="number" id="jml" name="jumlah" class="form-control" min="1" required>
                    </div>
                </div>
                <button class="btn btn-ink w-100">Simpan Pengeluaran</button>
            </form>
        </div>

        <?php if (! empty($daftarPengeluaran)): ?>
            <div class="panel mt-4">
                <div class="panel-head"><h2>Daftar pengeluaran</h2></div>
                <table class="tbl">
                    <tbody>
                    <?php foreach ($daftarPengeluaran as $p): ?>
                        <tr>
                            <td>
                                <?= esc($p->getKategori()) ?>
                                <div class="subtle" style="font-size:12.5px"><?= tanggal($p->getTanggal()) ?> &middot; <?= rupiah($p->getJumlah()) ?></div>
                            </td>
                            <td class="num">
                                <form method="post" action="<?= site_url('kas/hapus/' . $p->getId()) ?>" onsubmit="return confirm('Hapus pengeluaran ini?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="filterBulan" value="<?= esc($bulan) ?>">
                                    <button class="btn btn-line btn-icon danger" title="Hapus"><i class="bi bi-trash3"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
    </div>
    <?php endif ?>
</div>
