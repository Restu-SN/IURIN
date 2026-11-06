</main>
<footer class="foot">
    <span>Iurin &mdash; <?= esc($klub) ?></span>
    <span>Kas &amp; iuran main</span>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Kolom nominal otomatis diberi titik ribuan saat diketik: 10000 -> 10.000
    document.querySelectorAll('.input-rupiah').forEach(function (el) {
        el.addEventListener('input', function () {
            var posisi = el.value.slice(0, el.selectionStart).replace(/\D/g, '').length;
            var angka = el.value.replace(/\D/g, '').replace(/^0+(?=\d)/, '');
            el.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            // kembalikan kursor ke posisi angka yang sama
            var i = 0, n = 0;
            while (i < el.value.length && n < posisi) { if (/\d/.test(el.value[i])) n++; i++; }
            el.setSelectionRange(i, i);
        });
    });
</script>
</body>
</html>
