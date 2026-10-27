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

/* Thumbnail foto & tombol unduh dokumen lama di modal edit, setinggi input file */
.file-current{
    width: 46px;
    height: 46px;
    object-fit: cover;
    background-color: #f8f9fa;
    font-size: 0; /* sembunyikan teks alt selama gambar dimuat */
}
html.app-skin-dark .file-current{
    background-color: #1b2436;
}

/* Tombol hapus pilihan Select2 4.1 berupa <button>; buang bingkai bawaan browser */
.select2-container--bootstrap-5 .select2-selection__clear{
    border: 0;
}
</style>

@php
    // Format tanggal untuk tampilan tabel & detail
    $dateText = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d-F-Y') : null;
@endphp

<div class="main-content">
    <div class="card stretch stretch-full">
        <div class="d-flex justify-content-between align-items-center p-3">
            <h5 class="card-title mb-0">Data Client</h5>
            <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#createClientModal">
                <i class="feather-plus me-1"></i> Create
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle mb-0"
                       id="clientList"
                       style="min-width: 900px;">

                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Client Name</th>
                            <th>Standard</th>
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <form id="createClientForm" action="{{ route('client.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Data Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Client Name</label>
                            <select name="client_name" class="form-control tag-select" data-placeholder="-- Select or type Client Name --">
                                <option value=""></option>
                                @foreach($clientNames as $name)
                                    <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger small error-text" id="error_client_name"></span>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">Sales Name</label>
                                    <select name="sales_name" class="form-control tag-select" data-placeholder="Select or type" data-phone-field="sales_phone">
                                        <option value=""></option>
                                        @foreach($salesOptions['names'] as $sales)
                                            <option value="{{ $sales['name'] }}" data-hint="{{ $sales['phone'] }}">{{ $sales['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger small error-text" id="error_sales_name"></span>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">No. HP</label>
                                    <select name="sales_phone" class="form-control tag-select phone-select" data-placeholder="Select or type" data-name-field="sales_name">
                                        <option value=""></option>
                                        @foreach($salesOptions['phones'] as $sales)
                                            <option value="{{ $sales['phone'] }}" data-hint="{{ $sales['name'] }}">{{ $sales['phone'] }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger small error-text" id="error_sales_phone"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Scope</label>
                            <input type="text" name="scope" class="form-control">
                            <span class="text-danger small error-text" id="error_scope"></span>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Legalitas</label>
                            <input type="text" name="legality" class="form-control">
                            <span class="text-danger small error-text" id="error_legality"></span>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">PIC Name</label>
                                    <select name="pic_name" class="form-control tag-select" data-placeholder="Select or type" data-phone-field="pic_phone">
                                        <option value=""></option>
                                        @foreach($picOptions['names'] as $pic)
                                            <option value="{{ $pic['name'] }}" data-hint="{{ $pic['phone'] }}">{{ $pic['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger small error-text" id="error_pic_name"></span>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">No. HP</label>
                                    <select name="pic_phone" class="form-control tag-select phone-select" data-placeholder="Select or type" data-name-field="pic_name">
                                        <option value=""></option>
                                        @foreach($picOptions['phones'] as $pic)
                                            <option value="{{ $pic['phone'] }}" data-hint="{{ $pic['name'] }}">{{ $pic['phone'] }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger small error-text" id="error_pic_phone"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Foto <small class="fw-normal text-muted">· JPG, PNG, WEBP, maks 2 MB</small></label>
                            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <span class="text-danger small error-text" id="error_photo"></span>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Standard</label>
                            <select name="iso_standard" class="form-control tag-select" data-placeholder="-- Select or type Standard --">
                                <option value=""></option>
                                @foreach($isoOptions as $iso)
                                    <option value="{{ $iso }}">{{ $iso }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger small error-text" id="error_iso_standard"></span>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Dokumen <small class="fw-normal text-muted">· semua jenis file, maks 5 MB</small></label>
                            <input type="file" name="document" class="form-control">
                            <span class="text-danger small error-text" id="error_document"></span>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="form-label">IA</label>
                            <input type="date" name="ia_date" class="form-control">
                            <span class="text-danger small error-text" id="error_ia_date"></span>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">SA1</label>
                            <input type="date" name="sa1_date" class="form-control">
                            <span class="text-danger small error-text" id="error_sa1_date"></span>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="form-label">SA2</label>
                            <input type="date" name="sa2_date" class="form-control">
                            <span class="text-danger small error-text" id="error_sa2_date"></span>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">RC</label>
                            <input type="date" name="rc_date" class="form-control">
                            <span class="text-danger small error-text" id="error_rc_date"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-light">Clear</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================= MODAL UPDATE ================= --}}
@foreach($allClient as $row)
<div class="modal fade" id="updateClientModal{{ $row->id_client }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <form class="updateClientForm" data-id="{{ $row->id_client }}" action="{{ route('client.update', $row->id_client) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Edit Data Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Client Name</label>
                            <select name="client_name" class="form-control tag-select" data-placeholder="-- Select or type Client Name --">
                                <option value=""></option>
                                @foreach($clientNames as $name)
                                    <option value="{{ $name }}" {{ strcasecmp($name, $row->client_name) === 0 ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger small error-text" id="error_client_name_{{ $row->id_client }}"></span>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">Sales Name</label>
                                    <select name="sales_name" class="form-control tag-select" data-placeholder="Select or type" data-phone-field="sales_phone">
                                        <option value=""></option>
                                        @foreach($salesOptions['names'] as $sales)
                                            <option value="{{ $sales['name'] }}" data-hint="{{ $sales['phone'] }}" {{ strcasecmp($sales['name'], $row->sales_name ?? '') === 0 ? 'selected' : '' }}>{{ $sales['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger small error-text" id="error_sales_name_{{ $row->id_client }}"></span>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">No. HP</label>
                                    <select name="sales_phone" class="form-control tag-select phone-select" data-placeholder="Select or type" data-name-field="sales_name">
                                        <option value=""></option>
                                        @foreach($salesOptions['phones'] as $sales)
                                            <option value="{{ $sales['phone'] }}" data-hint="{{ $sales['name'] }}" {{ $sales['phone'] === $row->sales_phone ? 'selected' : '' }}>{{ $sales['phone'] }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger small error-text" id="error_sales_phone_{{ $row->id_client }}"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Scope</label>
                            <input type="text" name="scope" value="{{ $row->scope }}" class="form-control">
                            <span class="text-danger small error-text" id="error_scope_{{ $row->id_client }}"></span>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Legalitas</label>
                            <input type="text" name="legality" value="{{ $row->legality }}" class="form-control">
                            <span class="text-danger small error-text" id="error_legality_{{ $row->id_client }}"></span>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">PIC Name</label>
                                    <select name="pic_name" class="form-control tag-select" data-placeholder="Select or type" data-phone-field="pic_phone">
                                        <option value=""></option>
                                        @foreach($picOptions['names'] as $pic)
                                            <option value="{{ $pic['name'] }}" data-hint="{{ $pic['phone'] }}" {{ strcasecmp($pic['name'], $row->pic_name ?? '') === 0 ? 'selected' : '' }}>{{ $pic['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger small error-text" id="error_pic_name_{{ $row->id_client }}"></span>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">No. HP</label>
                                    <select name="pic_phone" class="form-control tag-select phone-select" data-placeholder="Select or type" data-name-field="pic_name">
                                        <option value=""></option>
                                        @foreach($picOptions['phones'] as $pic)
                                            <option value="{{ $pic['phone'] }}" data-hint="{{ $pic['name'] }}" {{ $pic['phone'] === $row->pic_phone ? 'selected' : '' }}>{{ $pic['phone'] }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger small error-text" id="error_pic_phone_{{ $row->id_client }}"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Foto <small class="fw-normal text-muted">· JPG, PNG, WEBP, maks 2 MB</small></label>
                            <div class="d-flex gap-2">
                                @if($row->photo_mime)
                                    <a href="{{ route('client.photo', $row->id_client) }}" target="_blank" class="flex-shrink-0" title="Lihat foto saat ini">
                                        <img src="{{ route('client.photo', $row->id_client) }}" class="file-current rounded border" loading="lazy" alt="Foto saat ini">
                                    </a>
                                @endif
                                <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                            </div>
                            <span class="text-danger small error-text" id="error_photo_{{ $row->id_client }}"></span>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Standard</label>
                            <select name="iso_standard" class="form-control tag-select" data-placeholder="-- Select or type Standard --">
                                <option value=""></option>
                                @php
                                    // Standard tersimpan yang tidak ada di daftar tetap tampil sebagai pilihan
                                    $options = $isoOptions;
                                    if ($row->iso_standard && ! $options->contains(fn ($iso) => strcasecmp($iso, $row->iso_standard) === 0)) {
                                        $options = $options->concat([$row->iso_standard]);
                                    }
                                @endphp
                                @foreach($options as $iso)
                                    <option value="{{ $iso }}" {{ strcasecmp($iso, $row->iso_standard ?? '') === 0 ? 'selected' : '' }}>{{ $iso }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger small error-text" id="error_iso_standard_{{ $row->id_client }}"></span>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Dokumen <small class="fw-normal text-muted">· semua jenis file, maks 5 MB</small></label>
                            <div class="d-flex gap-2">
                                @if($row->document_name)
                                    <a href="{{ route('client.document', $row->id_client) }}" class="file-current btn btn-light border flex-shrink-0 d-flex align-items-center justify-content-center p-0"
                                        title="Unduh dokumen saat ini: {{ $row->document_name }}" aria-label="Unduh dokumen saat ini: {{ $row->document_name }}">
                                        <i class="bi bi-download"></i>
                                    </a>
                                @endif
                                <input type="file" name="document" class="form-control">
                            </div>
                            <span class="text-danger small error-text" id="error_document_{{ $row->id_client }}"></span>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="form-label">IA</label>
                            <input type="date" name="ia_date" value="{{ $row->ia_date }}" class="form-control">
                            <span class="text-danger small error-text" id="error_ia_date_{{ $row->id_client }}"></span>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">SA1</label>
                            <input type="date" name="sa1_date" value="{{ $row->sa1_date }}" class="form-control">
                            <span class="text-danger small error-text" id="error_sa1_date_{{ $row->id_client }}"></span>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="form-label">SA2</label>
                            <input type="date" name="sa2_date" value="{{ $row->sa2_date }}" class="form-control">
                            <span class="text-danger small error-text" id="error_sa2_date_{{ $row->id_client }}"></span>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">RC</label>
                            <input type="date" name="rc_date" value="{{ $row->rc_date }}" class="form-control">
                            <span class="text-danger small error-text" id="error_rc_date_{{ $row->id_client }}"></span>
                        </div>

                        <div class="col-12">
                            <div class="form-text mt-0">Foto dan dokumen lama tetap tersimpan jika tidak memilih file baru.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="reset" class="btn btn-light">Clear</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
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
                    'Sales Name' => $row->sales_name,
                    'No. HP Sales' => $row->sales_phone,
                    'Scope' => $row->scope,
                    'Legalitas' => $row->legality,
                    'PIC Name' => $row->pic_name,
                    'No. HP PIC' => $row->pic_phone,
                    'Standard' => $row->iso_standard,
                    'IA' => $dateText($row->ia_date),
                    'SA1' => $dateText($row->sa1_date),
                    'SA2' => $dateText($row->sa2_date),
                    'RC' => $dateText($row->rc_date),
                ] as $label => $value)
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">{{ $label }}</div>
                    <div class="col-md-8">{{ $value ?: '-' }}</div>
                </div>
                @endforeach
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Foto</div>
                    <div class="col-md-8">
                        @if($row->photo_mime)
                            <a href="{{ route('client.photo', $row->id_client) }}" target="_blank">
                                <img src="{{ route('client.photo', $row->id_client) }}" width="150" class="img-thumbnail" loading="lazy" alt="Foto">
                            </a>
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

    // Client Name, Sales, PIC, No. HP & Standard: pilih dari daftar atau ketik entri baru.
    // Entri baru ikut tersimpan bersama data client, jadi muncul di daftar pada input berikutnya.
    $('.tag-select').each(function(){
        let select = $(this);
        let isPhone = select.hasClass('phone-select');
        let picked = false;

        select.select2({
            theme: 'bootstrap-5',
            tags: true,
            allowClear: true,
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
                if(err.status === 422){
                    let errors = err.responseJSON.errors;
                    $.each(errors, function(key,val){
                        $('#error_'+key).text(val[0]);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Server error occurred.'
                    });
                }
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
                if(err.status === 422){
                    let errors = err.responseJSON.errors;
                    $.each(errors, function(key,val){
                        $('#error_'+key+'_'+id).text(val[0]);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Server error occurred.'
                    });
                }
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
