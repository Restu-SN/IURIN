<%@ include file="_header.jsp" %>
<div class="page-head">
    <div>
        <div class="eyebrow">Akun</div>
        <h1>Ganti Password</h1>
    </div>
</div>

<div class="panel" style="max-width:460px">
    <form class="panel-body" method="post" action="${ctx}/password">
        <div class="mb-3">
            <label class="form-label" for="lama">Password lama</label>
            <input type="password" id="lama" name="lama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="baru">Password baru</label>
            <input type="password" id="baru" name="baru" class="form-control" minlength="6" required>
            <div class="form-text">Minimal 6 karakter.</div>
        </div>
        <div class="mb-4">
            <label class="form-label" for="ulang">Ulangi password baru</label>
            <input type="password" id="ulang" name="ulang" class="form-control" minlength="6" required>
        </div>
        <button class="btn btn-ink">Simpan Password</button>
    </form>
</div>
<%@ include file="_footer.jsp" %>
