<div class="page-head">
    <div>
        <div class="eyebrow">Iuran mingguan</div>
        <h1>Pembayaran</h1>
    </div>
</div>

<div class="row g-4">
    <?php if ($isAdmin): ?>
    <div class="col-lg-5">
        <div class="panel">
            <div class="panel-head"><h2>Catat pembayaran</h2></div>
            <form class="panel-body" method="post" action="<?= site_url('pembayaran') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label" for="anggotaId">Anggota</label>
                    <select name="anggotaId" id="anggotaId" class="form-select" required>
                        <option value="">Pilih anggota</option>
                        <?php foreach ($daftarAnggota as $a): ?>
                            <option value="<?= $a->getId() ?>" data-iuran="<?= $a->getNominalIuran() ?>"
                                <?= ($input['anggotaId'] ?? 0) === $a->getId() ? 'selected' : '' ?>><?= esc($a->getNama()) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6 col-lg-12 col-xl-6">
                        <label class="form-label" for="mingguBayar">Minggu iuran</label>
                        <input type="week" id="mingguBayar" name="minggu" class="form-control" required placeholder="2026-W45"
                               value="<?= esc($input['minggu'] ?? $minggu) ?>">
                    </div>
                    <div class="col-sm-6 col-lg-12 col-xl-6">
                        <label class="form-label" for="tanggalBayar">Tanggal bayar</label>
                        <input type="date" id="tanggalBayar" name="tanggalBayar" class="form-control" required
                               value="<?= esc($input['tanggalBayar'] ?? $hariIni) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="jumlah">Nominal</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">Rp</span>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" required
                               value="<?= ($input['jumlah'] ?? 0) > 0 ? $input['jumlah'] : '' ?>">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="keterangan">Keterangan</label>
                    <input type="text" id="keterangan" name="keterangan" class="form-control" maxlength="255" placeholder="Tunai / Transfer"
                           value="<?= esc($input['keterangan'] ?? '') ?>">
                </div>
                <button class="btn btn-ink w-100">Simpan Pembayaran</button>
            </form>
        </div>
    </div>
    <?php endif ?>

    <div class="<?= $isAdmin ? 'col-lg-7' : 'col-12' ?>">
        <div class="panel">
            <div class="panel-head flex-wrap">
                <div>
                    <h2>Riwayat</h2>
                    <div class="subtle"><?= esc($namaMinggu) ?></div>
                </div>
                <form method="get" action="<?= site_url('pembayaran') ?>">
                    <input type="week" name="minggu" class="form-control form-control-sm" value="<?= esc($minggu) ?>"
                           onchange="this.form.submit()" aria-label="Pilih minggu">
                </form>
            </div>
            <div class="table-responsive">
                <table class="tbl">
                    <thead><tr><th>Anggota</th><th class="wide">Tanggal</th><th class="num">Jumlah</th><?php if ($isAdmin): ?><th></th><?php endif ?></tr></thead>
                    <tbody>
                    <?php foreach ($riwayat as $p): ?>
                        <tr>
                            <td>
                                <div class="person">
                                    <span class="avatar"><?= esc(inisial($p->getAnggota()->getNama())) ?></span>
                                    <div>
                                        <?= esc($p->getAnggota()->getNama()) ?>
                                        <div class="subtle" style="font-size:12.5px"><?= esc($p->getKeterangan() ?: '-') ?><span class="d-sm-none"> &middot; <?= tanggal($p->getTanggalBayar()) ?></span></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-nowrap wide"><?= tanggal($p->getTanggalBayar()) ?></td>
                            <td class="num amount-in"><?= rupiah($p->getJumlah()) ?></td>
                            <?php if ($isAdmin): ?>
                                <td class="num">
                                    <form method="post" action="<?= site_url('pembayaran/hapus/' . $p->getId()) ?>" onsubmit="return confirm('Hapus pembayaran ini?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="minggu" value="<?= esc($minggu) ?>">
                                        <button class="btn btn-line btn-icon danger" title="Hapus"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            <?php endif ?>
                        </tr>
                    <?php endforeach ?>
                    <?php if (empty($riwayat)): ?>
                        <tr><td colspan="4" class="empty"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada pembayaran minggu ini.</td></tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php if ($isAdmin): ?>
<script>
    // Isi nominal otomatis sesuai iuran anggota yang dipilih
    document.getElementById('anggotaId').addEventListener('change', function () {
        var opt = this.options[this.selectedIndex];
        if (opt.dataset.iuran) document.getElementById('jumlah').value = opt.dataset.iuran;
    });
</script>
<?php endif ?>
