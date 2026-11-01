{{-- Filter rentang tanggal (+ status), urutan, dan download rekap Excel/PDF untuk tabel DataTables.
     $dates = [index kolom => label], $sorts = ['kolom:arah' => label], $status = ['column' => index, 'options' => [...]].
     Halaman memanggil setupListToolbar(table) setelah DataTable dibuat; sel tanggal perlu data-order (Y-m-d), baris perlu data-id.
     Toolbar disembunyikan sampai dipindah ke bawah baris Show entries / Search milik DataTables. --}}
@props(['table', 'export', 'dates', 'sorts', 'status' => null])

<div class="list-toolbar" data-table="{{ $table }}" data-export="{{ $export }}" hidden>
    <div class="list-toolbar-group">
        <span class="lt-label">Tanggal</span>
        @if(count($dates) > 1)
            <select class="form-select form-select-sm lt-date-column" aria-label="Filter tanggal berdasarkan">
                @foreach($dates as $column => $label)
                    <option value="{{ $column }}">{{ $label }}</option>
                @endforeach
            </select>
        @else
            <span class="lt-label lt-date-label">{{ reset($dates) }}</span>
            <input type="hidden" class="lt-date-column" value="{{ array_key_first($dates) }}">
        @endif
        <input type="date" class="form-control form-control-sm lt-date-from" aria-label="Dari tanggal">
        <span class="lt-label">s/d</span>
        <input type="date" class="form-control form-control-sm lt-date-to" aria-label="Sampai tanggal">
        @if($status)
            {{-- $status['badge'] (opsional): prefix class badge, pilihan tampil sebagai badge berwarna mis. "cert-status cert-status-active" --}}
            <select class="form-select form-select-sm lt-status" data-column="{{ $status['column'] }}" aria-label="Filter status">
                <option value="">Semua status</option>
                @foreach($status['options'] as $option)
                    <option value="{{ $option }}" @isset($status['badge']) data-badge="{{ $status['badge'].strtolower($option) }}" @endisset>{{ $option }}</option>
                @endforeach
            </select>
        @endif
        <button type="button" class="btn btn-sm btn-light border lt-reset" title="Reset filter" aria-label="Reset filter" hidden>
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="list-toolbar-group">
        <select class="form-select form-select-sm lt-sort" aria-label="Urutkan data">
            <option value="" hidden>Urutkan</option>
            @foreach($sorts as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <button type="button" class="btn btn-sm btn-success lt-export" data-format="xlsx" title="Download rekap Excel sesuai filter">
            <i class="bi bi-file-earmark-excel me-1"></i>Excel
        </button>
        <button type="button" class="btn btn-sm btn-danger lt-export" data-format="pdf" title="Download rekap PDF sesuai filter">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
        </button>
    </div>
</div>

@once
<style>
.list-toolbar{
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 0.75rem 1.5rem;
    /* Sejajar dengan isi baris Show entries / Search DataTables (padding 25px) */
    padding: 0 25px 20px;
}
.list-toolbar-group{
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
}
/* Theme memberi input padding 12px atas-bawah, terlalu besar untuk tinggi 34px sehingga teks terpotong */
.list-toolbar .form-control,
.list-toolbar .form-select{
    width: auto;
    height: 34px;
    padding: 0 12px;
    font-size: 13px;
    line-height: 32px;
}
.list-toolbar input[type="date"]{
    width: 140px;
}
.list-toolbar .form-select{
    min-width: 130px;
    padding-right: 2rem;
}
.list-toolbar .btn{
    height: 34px;
    padding: 0 14px;
    font-size: 13px;
}
/* Dropdown status berbadge (Select2) setinggi kontrol toolbar lain */
.list-toolbar .select2-container{
    width: 160px !important; /* theme memaksa Select2 100% lebar */
}
.list-toolbar .select2-container--bootstrap-5 .select2-selection--single{
    display: flex;
    align-items: center;
    height: 34px;
    min-height: 34px;
    padding: 0 2rem 0 10px;
    font-size: 13px;
}
/* Dark mode: latar & teks sama dengan dropdown biasa di sebelahnya */
html.app-skin-dark .list-toolbar .select2-container--bootstrap-5 .select2-selection{
    background-color: transparent;
}
html.app-skin-dark .list-toolbar .select2-container--bootstrap-5 .select2-selection__rendered{
    color: #fff;
}
.list-toolbar .lt-reset{
    width: 34px;
    padding: 0;
}
.list-toolbar .lt-label{
    font-size: 13px;
    font-weight: 600;
    color: #283c50;
}
html.app-skin-dark .list-toolbar .lt-label{
    color: #d1d5db;
}
</style>
@push('scripts')
<script>
window.setupListToolbar = function (table) {
    let tableId = table.table().node().id;
    let bar = $('.list-toolbar[data-table="' + tableId + '"]');
    let dateColumn = bar.find('.lt-date-column');
    let from = bar.find('.lt-date-from');
    let to = bar.find('.lt-date-to');
    let status = bar.find('.lt-status');
    let sort = bar.find('.lt-sort');

    // Search di atas, filter/urutan/download di bawahnya
    $(table.table().container()).find('.dataTables_filter').closest('.row').after(bar);
    bar.prop('hidden', false);

    // Nomor urut mengikuti urutan & filter yang sedang tampil
    function renumber() {
        table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.textContent = i + 1;
        });
    }
    table.on('draw', renumber);
    renumber();

    // Filter rentang tanggal, dibandingkan dengan nilai Y-m-d di atribut data-order sel tanggal
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        if (settings.nTable.id !== tableId || (!from.val() && !to.val())) {
            return true;
        }
        let value = settings.aoData[dataIndex].anCells[parseInt(dateColumn.val(), 10)].getAttribute('data-order') || '';
        return value !== '' && (!from.val() || value >= from.val()) && (!to.val() || value <= to.val());
    });

    function applyFilter() {
        from.attr('max', to.val() || null);
        to.attr('min', from.val() || null);
        if (status.length) {
            let value = status.val();
            table.column(status.data('column')).search(value ? '^' + $.fn.dataTable.util.escapeRegex(value) + '$' : '', true, false);
        }
        bar.find('.lt-reset').prop('hidden', !(from.val() || to.val() || status.val()));
        table.draw();
    }
    bar.on('change', '.lt-date-column, .lt-date-from, .lt-date-to, .lt-status', applyFilter);
    bar.on('click', '.lt-reset', function () {
        from.val('');
        to.val('');
        status.val('').trigger('change.select2');
        applyFilter();
    });

    // Status dengan warna identitas: pilihan & nilai terpilih tampil sebagai badge (Select2)
    if (status.find('option[data-badge]').length) {
        let badge = function (item) {
            let badgeClass = item.element && item.element.getAttribute('data-badge');
            return badgeClass ? $('<span>').addClass(badgeClass).text(item.text) : item.text;
        };
        status.select2({
            theme: 'bootstrap-5',
            width: '150px',
            minimumResultsForSearch: Infinity,
            templateResult: badge,
            templateSelection: badge,
        });
    }

    // Urutkan: pilihan ikut berubah bila urutan diganti lewat klik judul kolom
    sort.on('change', function () {
        let parts = this.value.split(':');
        table.order([parseInt(parts[0], 10), parts[1]]).draw();
    });
    function syncSort() {
        let order = table.order();
        let value = order.length === 1 ? order[0][0] + ':' + order[0][1] : '';
        sort.val(sort.find('option[value="' + value + '"]').length ? value : '');
    }
    table.on('order', syncSort);
    syncSort();

    // Keterangan filter untuk judul rekap
    function filterText() {
        let date = function (iso) { return iso.split('-').reverse().join('-'); };
        let parts = [];
        if (from.val() || to.val()) {
            let label = 'Tanggal ' + (bar.find('.lt-date-label').text() || dateColumn.find('option:selected').text());
            if (from.val() && to.val()) {
                parts.push(label + ' ' + date(from.val()) + ' s/d ' + date(to.val()));
            } else {
                parts.push(label + (from.val() ? ' mulai ' + date(from.val()) : ' sampai ' + date(to.val())));
            }
        }
        if (status.val()) {
            parts.push('Status ' + status.val());
        }
        if (table.search()) {
            parts.push('Pencarian "' + table.search() + '"');
        }
        return parts.join('; ');
    }

    // Download rekap: kirim id baris yang lolos filter & pencarian, dalam urutan yang tampil (semua halaman)
    bar.on('click', '.lt-export', function () {
        let ids = table.rows({ search: 'applied', order: 'applied' }).nodes().toArray().map(function (row) {
            return row.getAttribute('data-id');
        });
        if (!ids.length) {
            Swal.fire({ icon: 'info', title: 'Tidak ada data', text: 'Tidak ada data yang cocok dengan filter saat ini.' });
            return;
        }
        let form = $('<form method="POST" hidden>').attr('action', bar.data('export')).append(
            $('<input type="hidden" name="_token">').val($('meta[name="csrf-token"]').attr('content')),
            $('<input type="hidden" name="format">').val($(this).data('format')),
            $('<input type="hidden" name="ids">').val(ids.join(',')),
            $('<input type="hidden" name="filter">').val(filterText())
        ).appendTo('body');
        form[0].submit();
        form.remove();
    });
};
</script>
@endpush
@endonce
