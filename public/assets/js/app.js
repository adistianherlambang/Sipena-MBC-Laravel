document.addEventListener('DOMContentLoaded', function () {
    const navToggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-nav]');
    if (navToggle && nav) {
        navToggle.addEventListener('click', function () {
            nav.classList.toggle('open');
        });
    }

    const kategori = document.querySelector('#kategori');
    const kategoriLainWrap = document.querySelector('#kategori-lain-wrap');
    const strukWrap = document.querySelector('#struk-wrap');
    const kategoriLain = document.querySelector('#kategori_lain');
    const strukFile = document.querySelector('#struk_file');

    function syncPengaduanFields() {
        if (!kategori) return;
        const value = kategori.value;
        if (kategoriLainWrap) kategoriLainWrap.style.display = value === 'masalah_lain' ? 'block' : 'none';
        if (strukWrap) strukWrap.style.display = value === 'return_produk' ? 'block' : 'none';
        if (kategoriLain) kategoriLain.required = value === 'masalah_lain';
        if (strukFile) strukFile.required = value === 'return_produk';
    }

    if (kategori) {
        kategori.addEventListener('change', syncPengaduanFields);
        syncPengaduanFields();
    }
});
