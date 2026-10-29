@extends('layouts.app')

@section('title', 'Data Client')

@section('content')
<style>
/* Hover baris di modal detail, mengikuti mode terang/gelap */
.detail-row{
    transition: background 0.3s;
}
.detail-row:hover{
    background: #f8f9fa;
}
html.app-skin-dark .detail-row:hover{
    background: #1b2436;
}

/* Thumbnail foto & tombol unduh file lama di modal edit, setinggi input file */
.file-current{
    width: 40px;
    height: 40px;
    object-fit: cover;
    background-color: #f8f9fa;
    font-size: 0; /* sembunyikan teks alt selama gambar dimuat */
}
.file-current .bi{
    font-size: 15px;
}
html.app-skin-dark .file-current{
    background-color: #1b2436;
}

/* Tema memberi tiap field Select2 z-index 1, sehingga field di bawahnya (mis. Standard)
   menutupi dropdown yang sedang terbuka. Field yang terbuka dinaikkan di atas field lain. */
select + .select2-container--bootstrap-5.select2-container--open{
    z-index: 2;
}

/* ===== Form Data Client mengikuti mockup klien: label di kiri, field berbentuk pill bergaris ungu ===== */
.client-form{
    --client-line: #8a4fd0;
    --client-accent: #6a2bb5;
    --client-ring: rgba(138, 79, 208, 0.22);
    --client-label: 8.25rem; /* muat "Lampiran Sertifikat" */
}
/* Layar lebar (3 kolom): kolom kiri & tengah cukup label pendek agar input lebih lebar */
@media (min-width: 1200px){
    .client-form .client-col-main{
        --client-label: 6.75rem;
    }
}
html.app-skin-dark .client-form{
    --client-line: #a77de0;
    --client-accent: #c4a3ef;
    --client-ring: rgba(167, 125, 224, 0.3);
}

/* Satu baris field: label di kiri, input (+ pesan error) di kanan */
.client-field{
    display: grid;
    grid-template-columns: var(--client-label) minmax(0, 1fr);
    column-gap: 0.75rem;
    align-items: start;
    margin-bottom: 0.625rem;
}
.client-field > .form-label{
    min-height: 40px;
    display: flex;
    align-items: center;
    margin: 0;
    line-height: 1.25;
}

/* Jadwal audit: IA/SA1/SA2/RC dengan dua lampiran di bawahnya */
.client-stage{
    margin-top: 0.375rem;
}
.client-field-sub > .form-label{
    padding-left: 0.875rem;
    font-weight: 500 !important;
    opacity: 0.85;
}

.client-form .form-control,
html.app-skin-dark .client-form .form-control,
.client-form .select2-container--bootstrap-5 .select2-selection{
    border-color: var(--client-line) !important;
    border-radius: 50rem;
}
.client-form .form-control{
    height: 40px;
    padding: 0 16px;
    font-size: 13px;
}
.client-form .form-control:focus,
.client-form .select2-container--focus .select2-selection,
.client-form .select2-container--open .select2-selection{
    box-shadow: 0 0 0 0.2rem var(--client-ring) !important;
}

/* Select2 setinggi input lain, panah segitiga ungu seperti mockup */
.client-form .select2-container--bootstrap-5 .select2-selection--single{
    min-height: 40px;
    padding: 0 2.25rem 0 11px;
    display: flex;
    align-items: center;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%236a2bb5' d='M3 5.5h10L8 11z'/%3e%3c/svg%3e");
    background-size: 14px 14px;
    background-position: right 14px center;
}
html.app-skin-dark .client-form .select2-container--bootstrap-5 .select2-selection--single{
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%23c4a3ef' d='M3 5.5h10L8 11z'/%3e%3c/svg%3e");
}
.client-form .select2-container--bootstrap-5.select2-container--open .select2-selection{
    border-radius: 50rem;
}
.client-form .select2-container--bootstrap-5 .select2-dropdown,
html.app-skin-dark .client-form .select2-container--bootstrap-5 .select2-dropdown{
    margin-top: 4px;
    border-color: var(--client-line);
    border-top: 1px solid var(--client-line);
    border-radius: 12px;
    overflow: hidden;
}

/* Input file: tombol bawaan browser disembunyikan, ikon kamera/folder di kanan seperti mockup */
.client-file{
    display: flex;
    gap: 0.5rem;
}
.client-file-input{
    position: relative;
    flex: 1;
    min-width: 0;
}
.client-file-input .form-control{
    padding-right: 2.75rem;
    line-height: 38px;
    cursor: pointer;
}
.client-file-input .form-control::file-selector-button{
    display: none;
}
.client-file-input > .bi{
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    font-size: 17px;
    line-height: 1;
    color: var(--client-accent);
    pointer-events: none;
}
/* Ikon kalender bawaan Chrome diganti ikon ungu yang sama gayanya dengan ikon folder */
.client-form input[type="date"]::-webkit-calendar-picker-indicator{
    width: 17px;
    height: 17px;
    margin-right: -1px;
    background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236a2bb5'%3e%3cpath d='M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857z'/%3e%3cpath d='M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2'/%3e%3c/svg%3e") center / contain no-repeat;
    cursor: pointer;
}
html.app-skin-dark .client-form input[type="date"]::-webkit-calendar-picker-indicator{
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23c4a3ef'%3e%3cpath d='M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857z'/%3e%3cpath d='M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2'/%3e%3c/svg%3e");
}

.client-hint{
    margin-top: 3px;
    padding-left: 16px;
    font-size: 11px;
    color: #6b7280;
}
.client-note{
    margin-top: 0.75rem;
    padding: 0.625rem 0.875rem;
    border-left: 3px solid var(--client-line);
    border-radius: 4px;
    background-color: rgba(138, 79, 208, 0.06);
    font-size: 12px;
    line-height: 1.6;
    color: #4b5563;
}
html.app-skin-dark .client-hint,
html.app-skin-dark .client-note{
    color: #9ca3af;
}
html.app-skin-dark .client-note{
    background-color: rgba(167, 125, 224, 0.08);
}

/* Tombol CLEAR (merah muda) & SAVE (hijau muda) seperti mockup */
.client-form .btn-client{
    min-width: 116px;
    padding: 10px 28px;
    font-size: 13px;
    color: #1f2937;
    border: 1px solid var(--client-line);
    border-radius: 50rem;
}
.client-form .btn-client-clear{
    background-color: #ffd9dc;
}
.client-form .btn-client-clear:hover,
.client-form .btn-client-clear:focus-visible{
    background-color: #ffc2c8;
    color: #1f2937;
}
.client-form .btn-client-save{
    background-color: #e3f9c8;
}
.client-form .btn-client-save:hover,
.client-form .btn-client-save:focus-visible{
    background-color: #cff3a4;
    color: #1f2937;
}
/* Tema memaksa teks tombol putih (saat fokus/klik, dan selalu di dark mode); latar tombol pastel, jadi teks tetap gelap */
.client-form .btn-client,
.client-form .btn-client:hover,
.client-form .btn-client:focus,
.client-form .btn-client:active,
html.app-skin-dark .client-form .btn-client,
html.app-skin-dark .client-form .btn-client:hover,
html.app-skin-dark .client-form .btn-client:focus,
html.app-skin-dark .client-form .btn-client:active{
    color: #1f2937 !important;
}
</style>

@php
    // Format tanggal untuk tampilan tabel & detail
    $dateText = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d-F-Y') : null;
@endphp

<div class="main-content">
    <div class="card stretch stretch-full">
        <div class="d-flex justify-content-between align-items-center p-3">
            <h5 class="card-title page-title mb-0">Data Client</h5>
            <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#createClientModal">
                <i class="feather-plus me-1"></i> Create
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle mb-0"
                       id="clientList"
                       style="min-width: 1000px;">

                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Client Name</th>
                            <th>Standard</th>
                            <th>CB</th>
                            <th>Sales Name</th>
                            <th>PIC Name</th>
                            <th>IA</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($allClient as $key => $row)
                        <tr id="clientRow{{ $row->id_client }}">

                            <td class="text-center">{{ $key + 1 }}</td>

                            <td>{{ $row->client_name }}</td>

                            <td>{{ $row->iso_standard ?: '-' }}</td>

                            <td>{{ $row->cb ?: '-' }}</td>

                            <td>
                                {{ $row->sales_name ?: '-' }}
                                @if($row->sales_phone)
                                    <div class="small text-muted">{{ $row->sales_phone }}</div>
                                @endif
                            </td>

                            <td>
                                {{ $row->pic_name ?: '-' }}
                                @if($row->pic_phone)
                                    <div class="small text-muted">{{ $row->pic_phone }}</div>
                                @endif
                            </td>

                            <td class="text-center">{{ $dateText($row->ia_date) ?: '-' }}</td>

                            <td>
                                <div class="d-flex align-items-center gap-1 flex-nowrap">
                                    <x-action-dropdown>
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#clientModal{{ $row->id_client }}">
                                                <i class="feather-eye me-2 text-info"></i>Lihat
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#updateClientModal{{ $row->id_client }}">
                                                <i class="feather-edit me-2 text-warning"></i>Edit
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button type="button" class="dropdown-item text-danger deleteClientBtn"
                                                data-url="{{ route('client.destroy', $row->id_client) }}"
                                                data-id="{{ $row->id_client }}">
                                                <i class="feather-trash-2 me-2"></i>Hapus
                                            </button>
                                        </li>
                                    </x-action-dropdown>
                                </div>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL CREATE ================= --}}
<div class="modal fade" id="createClientModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4">
            <form id="createClientForm" class="client-form" action="{{ route('client.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Data Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                @include('client.form', ['row' => null])
            </form>
        </div>
    </div>
</div>

{{-- ================= MODAL UPDATE ================= --}}
@foreach($allClient as $row)
<div class="modal fade" id="updateClientModal{{ $row->id_client }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4">
            <form class="updateClientForm client-form" data-id="{{ $row->id_client }}" action="{{ route('client.update', $row->id_client) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Edit Data Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                @include('client.form', ['row' => $row])
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- ================= MODAL DETAIL ================= --}}
@foreach($allClient as $row)
<div class="modal fade" id="clientModal{{ $row->id_client }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-sm">
            <!-- Header -->
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Client Detail</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                @foreach([
                    'Client Name' => $row->client_name,
                    'Scope' => $row->scope,
                    'CB' => $row->cb,
                    'PIC Name' => $row->pic_name,
                    'No. HP PIC' => $row->pic_phone,
                    'Sales Name' => $row->sales_name,
                    'No. HP Sales' => $row->sales_phone,
                    'Legalitas' => $row->legality,
                    'Standard' => $row->iso_standard,
                ] as $label => $value)
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">{{ $label }}</div>
                    <div class="col-md-8">{{ $value ?: '-' }}</div>
                </div>
                @endforeach

                {{-- Jadwal audit beserta Lampiran Sertifikat & Report yang bisa diunduh --}}
                @php($files = $row->attachments->keyBy('type'))
                @foreach(\App\Models\ClientAttachment::STAGES as $stage => $stageLabel)
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">{{ $stageLabel }}</div>
                    <div class="col-md-8">
                        {{ $dateText($row->{$stage.'_date'}) ?: '-' }}
                        @foreach(\App\Models\ClientAttachment::KINDS as $kind => $kindLabel)
                            @php($file = $files->get($stage.'_'.$kind))
                            <div class="small mt-1">
                                <span class="text-muted">{{ $kindLabel }}:</span>
                                @if($file)
                                    <a href="{{ route('client.attachment', [$row->id_client, $stage.'_'.$kind]) }}">
                                        <i class="bi bi-download me-1"></i>{{ $file->file_name }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Foto</div>
                    <div class="col-md-8">
                        @if($row->photo_mime)
                            <a href="{{ route('client.photo', $row->id_client) }}" target="_blank">
                                <img src="{{ route('client.photo', $row->id_client) }}" width="150" class="img-thumbnail" loading="lazy" alt="Foto">
                            </a>
                            <div class="small mt-1">
                                <a href="{{ route('client.photo', ['id' => $row->id_client, 'download' => 1]) }}">
                                    <i class="bi bi-download me-1"></i>Download foto
                                </a>
                            </div>
                        @else
                            <span class="text-muted">No Foto</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Dokumen</div>
                    <div class="col-md-8">
                        @if($row->document_name)
                            <a href="{{ route('client.document', $row->id_client) }}">
                                <i class="bi bi-download me-1"></i>{{ $row->document_name }}
                            </a>
                        @else
                            <span class="text-muted">No Dokumen</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let table = $('#clientList').DataTable({
        pageLength: 10
    });

    // Dropdown selalu menempel tepat di bawah field. Bawaan Select2 (attachBody) menghitung
    // posisi dengan JS sehingga di dalam modal yang di-scroll bisa meleset atau berbalik ke atas.
    let s2 = $.fn.select2.amd.require;
    let Utils = s2('select2/utils');
    function AttachBelowField(decorated, $element, options){
        decorated.call(this, $element, options);
    }
    AttachBelowField.prototype.position = function(decorated, $dropdown, $container){
        $container.find('.dropdown-wrapper').removeAttr('aria-hidden').append($dropdown);
        $dropdown.addClass('select2-dropdown--below');
        $container.addClass('select2-container--below');
    };
    let tagDropdown = Utils.Decorate(
        Utils.Decorate(Utils.Decorate(s2('select2/dropdown'), s2('select2/dropdown/search')), s2('select2/dropdown/closeOnSelect')),
        AttachBelowField
    );

    // Nomor HP: angka dengan pemisah spasi / - / . / kurung, boleh diawali +, minimal 6 digit (sama dengan validasi server)
    const PHONE_PATTERN = /^\+?(?:[\s().-]*\d){6,}[\s().-]*$/;

    // Keterangan di samping pilihan: nomor HP terakhir untuk nama, nama pemilik untuk nomor HP
    function hintOf(item){
        return item.element ? ($(item.element).attr('data-hint') || '') : '';
    }

    // Pilih value tanpa membedakan huruf besar/kecil; value baru ditambahkan sebagai pilihan
    function setTagValue(select, value){
        let option = select.find('option').filter(function(){
            return this.value !== '' && this.value.toLowerCase() === value.toLowerCase();
        });
        if (!option.length) {
            option = $(new Option(value, value));
            select.append(option);
        }
        select.val(option.val()).trigger('change');
        return option[0];
    }

    // Client Name, CB, Sales, PIC, No. HP & Standard: pilih dari daftar atau ketik entri baru.
    // Entri baru ikut tersimpan bersama data client, jadi muncul di daftar pada input berikutnya.
    $('.tag-select').each(function(){
        let select = $(this);
        let isPhone = select.hasClass('phone-select');
        let picked = false;

        select.select2({
            theme: 'bootstrap-5',
            tags: true,
            width: '100%',
            dropdownAdapter: tagDropdown,
            // Cari juga lewat keterangan, mis. ketik nama Sales untuk menemukan nomornya
            matcher: function(params, item){
                let term = $.trim(params.term || '').toLowerCase();
                if (!term || (item.text + ' ' + hintOf(item)).toLowerCase().includes(term)) {
                    return item;
                }
                return null;
            },
            templateResult: function(item){
                let hint = hintOf(item);
                if (!hint) {
                    return item.text;
                }
                return $('<span>').append(
                    $('<span class="d-block">').text(item.text),
                    $('<small class="d-block opacity-75">').text(hint)
                );
            },
            createTag: function(params){
                let term = $.trim(params.term);
                if (!term || (isPhone && !PHONE_PATTERN.test(term))) {
                    return null;
                }
                return { id: term, text: term };
            },
            // Entri baru di urutan terakhir, sehingga data yang sudah ada yang tersorot lebih dulu
            insertTag: function(data, tag){
                data.push(tag);
            }
        }).on('select2:open', function(){
            picked = false;
            // Pastikan daftar pilihan terlihat penuh di dalam modal (setelah hasil selesai di-render)
            let dropdown = select.next('.select2-container').find('.select2-dropdown')[0];
            setTimeout(function(){
                dropdown.scrollIntoView({ block: 'nearest' });
            });
        }).on('select2:selecting', function(){
            picked = true;
        }).on('select2:closing', function(e){
            let original = e.params.args && e.params.args.originalEvent;
            if (original && (original.key === 'Escape' || original.which === 27)) {
                // Esc hanya menutup dropdown & membatalkan ketikan, modal jangan ikut tertutup
                original.stopPropagation();
                return;
            }
            // Sudah mengetik tapi field ditinggalkan (klik di luar) tanpa memilih: pakai pilihan yang tersorot,
            // sama seperti menekan Enter, agar ketikan tidak hilang
            let container = select.next('.select2-container');
            let term = $.trim(container.find('.select2-search__field').val());
            let highlighted = container.find('.select2-results__option--highlighted')[0];
            let data = highlighted && Utils.GetData(highlighted, 'data');
            if (picked || !term || !data || data.id === undefined) {
                return;
            }
            let option = setTagValue(select, String(data.id));
            select.trigger({ type: 'select2:select', params: { data: { id: option.value, element: option } } });
        });
    });

    // Nama Sales/PIC dipilih: isi nomor HP terakhir orang itu. Jika orang itu belum punya nomor,
    // kosongkan nomor yang tercatat milik orang lain (nomor baru yang belum ada pemiliknya dibiarkan).
    $('.tag-select[data-phone-field]').on('select2:select', function(e){
        let phone = $(this).closest('form').find('select[name="' + $(this).data('phone-field') + '"]');
        let known = hintOf(e.params.data);
        let owner = phone.find('option:selected').attr('data-hint');
        if (known) {
            setTagValue(phone, known);
        } else if (owner && owner.toLowerCase() !== String(e.params.data.id).toLowerCase()) {
            phone.val(null).trigger('change');
        }
    });

    // Nomor HP dipilih saat nama masih kosong: isi nama pemilik nomor itu
    $('.tag-select[data-name-field]').on('select2:select', function(e){
        let name = $(this).closest('form').find('select[name="' + $(this).data('name-field') + '"]');
        let owner = hintOf(e.params.data);
        if (owner && !name.val()) {
            setTagValue(name, owner);
        }
    });

    // Tombol Clear: hapus pesan error dan sinkronkan tampilan Select2 setelah form di-reset
    $('#createClientForm, .updateClientForm').on('reset', function(){
        let form = $(this);
        form.find('.error-text').text('');
        setTimeout(function(){
            form.find('.tag-select').trigger('change');
        });
    });

    // Gagal simpan: 422 tampilkan pesan di bawah field (dan gulir ke error pertama, karena form panjang),
    // 413 total upload melebihi batas ukuran request server
    function showSaveError(form, err, suffix){
        if (err.status === 422) {
            $.each(err.responseJSON.errors, function(key, val){
                $('#error_' + key + suffix).text(val[0]);
            });
            let first = form.find('.error-text').filter(function(){ return this.textContent; })[0];
            if (first) {
                first.closest('.client-field').scrollIntoView({ block: 'center' });
            }
        } else if (err.status === 413) {
            Swal.fire({
                icon: 'error',
                title: 'File terlalu besar',
                text: 'Total ukuran file yang di-upload melebihi batas server. Simpan sebagian file dulu, lalu tambahkan sisanya lewat Edit.'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Server error occurred.'
            });
        }
    }

    // ================= CREATE =================
    $('#createClientForm').submit(function(e){
        e.preventDefault();

        let form = $(this);
        let formData = new FormData(this);
        form.find('.error-text').text('');

        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){

                $('#createClientModal').modal('hide');
                form[0].reset();

                // Reload agar modal detail/edit (foto, dokumen) untuk data baru ikut ter-render
                Swal.fire({
                    icon: 'success',
                    title: 'Created!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(function(){
                    location.reload();
                });
            },
            error: function(err){
                showSaveError(form, err, '');
            }
        });
    });


    // ================= UPDATE =================
    $('.updateClientForm').submit(function(e){
        e.preventDefault();

        let form = $(this);
        let id = form.data('id');
        let formData = new FormData(this);
        form.find('.error-text').text('');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){

                $('#updateClientModal'+id).modal('hide');

                // Reload agar modal detail (foto, dokumen, jadwal audit) ikut ter-update
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(function(){
                    location.reload();
                });
            },
            error: function(err){
                showSaveError(form, err, '_' + id);
            }
        });
    });


    // ================= DELETE =================
    $(document).on('click', '.deleteClientBtn', function(){

        let url = $(this).data('url');
        let id = $(this).data('id');
        let row = $('#clientRow' + id);

        Swal.fire({
            title: 'Are you sure?',
            text: "This data will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {

            if(result.isConfirmed){

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { _method: 'DELETE' },
                    success: function(res){

                        table.row(row).remove().draw(false);

                        // Re-number
                        table.rows().every(function(index){
                            this.cell(index,0).data(index+1);
                        });

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(){
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Server error occurred.'
                        });
                    }
                });
            }
        });
    });

});
</script>
@endpush

@endsection
