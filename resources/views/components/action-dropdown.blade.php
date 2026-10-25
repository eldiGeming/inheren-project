{{-- Tombol aksi per baris tabel dalam bentuk dropdown. Isi <li> item lewat slot. --}}
<div class="dropdown action-dropdown">
    <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="dropdown" aria-expanded="false" title="Action" aria-label="Action">
        <i class="feather-more-horizontal"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        {{ $slot }}
    </ul>
</div>

@once
<style>
/* Di atas header dashboard (z-index 1025), di bawah modal (1050+) */
.action-dropdown .dropdown-menu{
    z-index: 1030;
}
</style>
@push('scripts')
<script>
// Menu aksi memakai posisi fixed agar tidak terpotong overflow .table-responsive / card.
// Instance dibuat saat diklik karena baris di halaman lain DataTables tidak ada di DOM. Dipasang di window
// (fase capture) supaya jalan sebelum handler Bootstrap yang juga capture di document.
window.addEventListener('click', function (e) {
    let toggle = e.target.closest('.action-dropdown [data-bs-toggle="dropdown"]');
    if (toggle && !bootstrap.Dropdown.getInstance(toggle)) {
        new bootstrap.Dropdown(toggle, {
            popperConfig: function (defaultConfig) {
                // Area di bawah header fixed dianggap tidak tersedia, dan menu digeser agar selalu muat di layar
                let header = document.querySelector('.nxl-header');
                let padding = { top: (header ? header.offsetHeight : 0) + 8, bottom: 8 };
                return Object.assign({}, defaultConfig, {
                    strategy: 'fixed',
                    modifiers: defaultConfig.modifiers.concat([
                        { name: 'flip', options: { padding: padding } },
                        { name: 'preventOverflow', options: { altAxis: true, tether: false, padding: padding } },
                    ]),
                });
            }
        });
    }
}, true);
</script>
@endpush
@endonce
