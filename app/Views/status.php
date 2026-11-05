<?php
$total = $jumlahLunas + $jumlahBelum;
$persen = $total === 0 ? 0 : round($jumlahLunas * 100 / $total);
?>
<div class="page-head">
    <div>
        <div class="eyebrow"><?= esc($namaMinggu) ?></div>
        <h1>Status Iuran</h1>
    </div>
    <form method="get" action="<?= site_url('status') ?>">
        <input type="week" name="minggu" class="form-control" value="<?= esc($minggu) ?>" onchange="this.form.submit()" aria-label="Pilih minggu">
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-sm-4">
        <div class="panel stat stat-ink">
            <div class="label"><i class="bi bi-check-circle"></i> Sudah lunas</div>
            <div class="value"><?= $jumlahLunas ?> <span class="fs-6 fw-normal text-secondary">/ <?= $total ?></span></div>
        </div>
    </div>
    <div class="col-6 col-sm-4">
        <div class="panel stat">
            <div class="label"><i class="bi bi-hourglass-split"></i> Belum bayar</div>
            <div class="value"><?= $jumlahBelum ?></div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="panel stat">
            <div class="label"><i class="bi bi-pie-chart"></i> Kepatuhan</div>
            <div class="value"><?= $persen ?>%</div>
            <div class="meter mt-2"><span style="width:<?= $persen ?>%"></span></div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="tbl">
            <thead><tr><th>Nama</th><th>Status</th><th class="num wide">Dibayar</th></tr></thead>
            <tbody>
            <?php foreach ($daftar as $s): $p = $s['pembayaran']; ?>
                <tr>
                    <td>
                        <div class="person">
                            <span class="avatar <?= $s['status'] === 'LUNAS' ? '' : 'avatar-line' ?>"><?= esc(inisial($s['anggota']->getNama())) ?></span>
                            <div>
                                <?= esc($s['anggota']->getNama()) ?>
                                <?php if ($p !== null): ?>
                                    <span class="meta"><?= rupiah($p->getJumlah()) ?> &middot; <?= tanggal($p->getTanggalBayar()) ?></span>
                                <?php endif ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if ($s['status'] === 'LUNAS'): ?>
                            <span class="pill pill-lunas"><i class="bi bi-check-lg"></i> Lunas</span>
                        <?php elseif ($s['status'] === 'MENUNGGAK'): ?>
                            <span class="pill pill-tunggak"><i class="bi bi-exclamation-lg"></i> Menunggak</span>
                        <?php else: ?>
                            <span class="pill pill-belum"><i class="bi bi-clock"></i> Belum bayar</span>
                        <?php endif ?>
                    </td>
                    <td class="num wide">
                        <?php if ($p !== null): ?>
                            <span class="amount-in"><?= rupiah($p->getJumlah()) ?></span>
                            <div class="subtle" style="font-size:12.5px"><?= tanggal($p->getTanggalBayar()) ?></div>
                        <?php else: ?>
                            <span class="subtle">&mdash;</span>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php if (empty($daftar)): ?>
                <tr><td colspan="3" class="empty">Belum ada anggota.</td></tr>
            <?php endif ?>
            </tbody>
        </table>
    </div>
</div>
<p class="subtle mt-3 mb-0" style="font-size:13px">
    <span class="pill pill-lunas me-1">Lunas</span> sudah bayar &nbsp;
    <span class="pill pill-belum me-1">Belum bayar</span> minggu berjalan &nbsp;
    <span class="pill pill-tunggak me-1">Menunggak</span> minggu sudah lewat
</p>
