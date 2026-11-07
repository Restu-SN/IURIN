<?php $edit = $anggota !== null && $anggota->getId() !== null ?>
<div class="page-head">
    <div>
        <div class="eyebrow"><a href="<?= site_url('anggota') ?>" class="text-decoration-none text-reset"><i class="bi bi-arrow-left"></i> Anggota</a></div>
        <h1><?= $edit ? 'Edit Anggota' : 'Tambah Anggota' ?></h1>
    </div>
</div>

<div class="panel" style="max-width:520px">
    <form class="panel-body" method="post" action="<?= site_url('anggota/simpan') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $edit ? $anggota->getId() : 0 ?>">
        <div class="mb-3">
            <label class="form-label" for="nama">Nama</label>
            <input type="text" id="nama" name="nama" class="form-control" required maxlength="100" autofocus
                   value="<?= esc($anggota?->getNama() ?? '') ?>">
        </div>
        <div class="mb-4">
            <label class="form-label" for="nominalIuran">Iuran sekali main</label>
            <div class="input-group">
                <span class="input-group-text bg-white">Rp</span>
                <input type="text" id="nominalIuran" name="nominalIuran" class="form-control input-rupiah" inputmode="numeric" required
                       value="<?= angka($anggota !== null ? $anggota->getNominalIuran() : 15000) ?>">
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-ink">Simpan</button>
            <a href="<?= site_url('anggota') ?>" class="btn btn-line">Batal</a>
        </div>
    </form>
</div>

<?php if ($edit): $r = $rincianTalangan ?? ['total' => 0, 'terpotong' => 0, 'sisa' => 0]; ?>
<div class="panel mt-4" style="max-width:520px">
    <div class="panel-head"><h2>Talangan</h2><span class="subtle">Sisa <?= rupiah($r['sisa']) ?></span></div>
    <div class="panel-body">
        <p class="subtle mb-3">Uang klub yang dibayari dulu oleh <?= esc($anggota->getNama()) ?> (mis. sewa lapangan).
            Tiap dia main, iurannya memotong talangan sampai habis.</p>
        <div class="kas-hitung mb-4">
            <div><span>Total ditalangin</span><span><?= rupiah($r['total']) ?></span></div>
            <div><span>Sudah terpotong dari main</span><span>&minus; <?= rupiah($r['terpotong']) ?></span></div>
            <div class="kas-total"><span>Sisa talangan</span><span><?= rupiah($r['sisa']) ?></span></div>
        </div>
        <form method="post" action="<?= site_url('anggota/talangan/' . $anggota->getId()) ?>" class="mb-3">
            <?= csrf_field() ?>
            <div class="row g-2">
                <div class="col-sm-6">
                    <label class="form-label" for="jumlahTalangan">Nominal</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">Rp</span>
                        <input type="text" id="jumlahTalangan" name="jumlah" class="form-control input-rupiah" inputmode="numeric" placeholder="150.000" required>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="tanggalTalangan">Tanggal</label>
                    <input type="date" id="tanggalTalangan" name="tanggal" class="form-control" value="<?= esc($hariIni) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label" for="ketTalangan">Untuk apa</label>
                    <input type="text" id="ketTalangan" name="keterangan" class="form-control" maxlength="255" value="Sewa lapangan">
                </div>
            </div>
            <button class="btn btn-ink w-100 mt-3">Tambah Talangan</button>
        </form>
    </div>
    <?php if ($daftarTalangan !== []): ?>
        <table class="tbl border-top-line">
            <tbody>
            <?php foreach ($daftarTalangan as $t): ?>
                <tr>
                    <td>
                        <?= esc($t->getKeterangan() ?: 'Talangan') ?>
                        <div class="subtle" style="font-size:12.5px"><?= esc(nama_tanggal($t->getTanggal())) ?></div>
                    </td>
                    <td class="num text-nowrap"><?= rupiah($t->getJumlah()) ?></td>
                    <td class="num" style="width:1%">
                        <form method="post" action="<?= site_url('anggota/talangan/hapus/' . $t->getId()) ?>" onsubmit="return confirm('Hapus catatan talangan ini?');">
                            <?= csrf_field() ?>
                            <button class="btn btn-line btn-icon danger" title="Hapus"><i class="bi bi-trash3"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>
</div>
<?php endif ?>
