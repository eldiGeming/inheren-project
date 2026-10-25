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
@push('scripts')
<script>
// Menu aksi memakai posisi fixed agar tidak terpotong overflow .table-responsive / card.
// Instance dibuat saat diklik karena baris di halaman lain DataTables tidak ada di DOM. Dipasang di window
// (fase capture) supaya jalan sebelum handler Bootstrap yang juga capture di document.
window.addEventListener('click', function (e) {
    let toggle = e.target.closest('.action-dropdown [data-bs-toggle="dropdown"]');
    if (toggle && !bootstrap.Dropdown.getInstance(toggle)) {
        new bootstrap.Dropdown(toggle, { popperConfig: { strategy: 'fixed' } });
    }
}, true);
</script>
@endpush
@endonce
