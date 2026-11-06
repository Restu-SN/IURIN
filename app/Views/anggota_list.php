<div class="page-head">
    <div>
        <h1>Anggota</h1>
    </div>
    <?php if ($isAdmin): ?>
        <a href="<?= site_url('anggota/tambah') ?>" class="btn btn-ink"><i class="bi bi-plus-lg me-1"></i> Tambah Anggota</a>
    <?php endif ?>
</div>

<div class="panel">
    <div class="panel-head">
        <form class="input-search flex-grow-1" method="get" action="<?= site_url('anggota') ?>" style="max-width:420px">
            <i class="bi bi-search"></i>
            <input type="search" name="q" class="form-control" placeholder="Cari nama anggota" value="<?= esc($q) ?>">
        </form>
        <span class="subtle d-none d-sm-inline"><?= count($daftar) ?> anggota</span>
    </div>
    <div class="table-responsive">
        <table class="tbl">
            <thead>
            <tr>
                <th>Nama</th>
                <th class="num wide">Iuran / main</th>
                <th>Bulan ini</th>
                <?php if ($isAdmin): ?><th class="num"></th><?php endif ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($daftar as $a): ?>
                <tr>
                    <td>
                        <div class="person">
                            <span class="avatar"><?= esc(inisial($a->getNama())) ?></span>
                            <div>
                                <?= esc($a->getNama()) ?>
                                <span class="meta"><?= rupiah($a->getNominalIuran()) ?>/main</span>
                            </div>
                        </div>
                    </td>
                    <td class="num wide"><?= rupiah($a->getNominalIuran()) ?></td>
                    <td>
                        <?php $r = $rekap[$a->getId()] ?? null; ?>
                        <?php if (($r['main'] ?? 0) > 0): ?>
                            <span class="pill pill-lunas"><?= $r['main'] ?>&times; main</span>
                        <?php else: ?>
                            <span class="pill pill-belum">Belum main</span>
                        <?php endif ?>
                        <?php if (! empty($r['terakhir'])): ?>
                            <div class="subtle" style="font-size:12px;margin-top:4px">Terakhir <?= esc(nama_tanggal(new DateTimeImmutable($r['terakhir']))) ?></div>
                        <?php endif ?>
                    </td>
                    <?php if ($isAdmin): ?>
                        <td class="num text-nowrap">
                            <form method="post" action="<?= site_url('anggota/hapus/' . $a->getId()) ?>" id="hapus<?= $a->getId() ?>" class="d-inline"
                                  onsubmit="return confirm('Hapus anggota ini beserta seluruh riwayat mainnya?');">
                                <?= csrf_field() ?>
                            </form>
                            <span class="d-none d-sm-inline">
                                <a href="<?= site_url('anggota/edit/' . $a->getId()) ?>" class="btn btn-line btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                                <button form="hapus<?= $a->getId() ?>" class="btn btn-line btn-icon danger" title="Hapus"><i class="bi bi-trash3"></i></button>
                            </span>
                            <div class="dropdown d-sm-none">
                                <button class="btn btn-line btn-icon" data-bs-toggle="dropdown" aria-label="Aksi"><i class="bi bi-three-dots"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?= site_url('anggota/edit/' . $a->getId()) ?>"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                    <li><button form="hapus<?= $a->getId() ?>" class="dropdown-item"><i class="bi bi-trash3 me-2"></i>Hapus</button></li>
                                </ul>
                            </div>
                        </td>
                    <?php endif ?>
                </tr>
            <?php endforeach ?>
            <?php if (empty($daftar)): ?>
                <tr><td colspan="4" class="empty"><i class="bi bi-people fs-3 d-block mb-2"></i>Belum ada anggota.</td></tr>
            <?php endif ?>
            </tbody>
        </table>
    </div>
</div>
