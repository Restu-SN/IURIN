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
            <label class="form-label" for="nominalIuran">Iuran per minggu</label>
            <div class="input-group">
                <span class="input-group-text bg-white">Rp</span>
                <input type="number" id="nominalIuran" name="nominalIuran" class="form-control" min="0" step="500" required
                       value="<?= $anggota !== null ? $anggota->getNominalIuran() : 15000 ?>">
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-ink">Simpan</button>
            <a href="<?= site_url('anggota') ?>" class="btn btn-line">Batal</a>
        </div>
    </form>
</div>
