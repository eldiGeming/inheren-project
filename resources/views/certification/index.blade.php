@extends('layouts.app')

@section('title', 'Certification')

@section('content')
<div class="main-content">
    <div class="card stretch stretch-full">
        <div class="d-flex justify-content-end p-3">
            <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#createCertificationModal">
                <i class="feather-plus me-1"></i> Create
            </button>
        </div>

        <div class="card-body p-0">
   <div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover table-borderless align-middle mb-0" 
               id="certificationList" 
               style="min-width: 900px;">
            
            <thead class="text-center">
                <tr>
                    <th>No</th>
                    <th>Company Name</th>
                    <th>Type Certificate</th>
                    <th>Number Certificate</th>
                    <th>Issues Date</th>
                    <th>Expired Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($allCertification as $key => $row)
                <tr id="certificationRow{{ $row->id_certification }}">

                    <td class="text-center">{{ $key + 1 }}</td>

                    <td>{{ $row->company->name_company }}</td>

                    <td>{{ $row->type->type_certificate }}</td>

                    <td>{{ $row->number_certificate }}</td>

                    <td class="text-center">{{ $row->issues_date_formatted }}</td>

                    <td class="text-center">{{ $row->expired_date_formatted }}</td>

                    <td>
                        <div class="d-flex align-items-center gap-1 flex-nowrap">

                            <!-- PDF Final -->
                            <a href="{{ route('certification.generatePdf', $row->id_certification) }}"
                                class="btn btn-sm btn-danger"
                                target="_blank"
                                title="Generate PDF Final">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                            </a>

                            <!-- PDF Draft -->
                            <a href="{{ route('certification.generatePdfDraft', $row->id_certification) }}"
                                class="btn btn-sm btn-warning"
                                target="_blank"
                                title="Generate PDF Draft">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <x-action-dropdown>
                                <li>
                                    <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                        data-bs-target="#certificationModal{{ $row->id_certification }}">
                                        <i class="feather-eye me-2 text-info"></i>Lihat
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                        data-bs-target="#updateCertificationModal{{ $row->id_certification }}">
                                        <i class="feather-edit me-2 text-warning"></i>Edit
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button type="button" class="dropdown-item text-danger deleteCertificationBtn"
                                        data-url="{{ route('certification.destroy', $row->id_certification) }}"
                                        data-id="{{ $row->id_certification }}">
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
<div class="modal fade" id="createCertificationModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <form id="createCertificationForm" action="{{ route('certification.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Data Based Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client Name</label>
                            <select name="id_company" class="form-control">
                                <option value="">-- Select Client --</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id_company }}">{{ $company->name_company }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger small error-text" id="error_id_company"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sales Name</label>
                            <input type="text" name="sales_name" class="form-control">
                            <span class="text-danger small error-text" id="error_sales_name"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Scope</label>
                            <input type="text" name="scope" class="form-control">
                            <span class="text-danger small error-text" id="error_scope"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Legalitas</label>
                            <input type="text" name="legality" class="form-control">
                            <span class="text-danger small error-text" id="error_legality"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">PIC Name</label>
                            <input type="text" name="pic_name" class="form-control">
                            <span class="text-danger small error-text" id="error_pic_name"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Foto</label>
                            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">JPG, PNG, atau WEBP, maks 2 MB</div>
                            <span class="text-danger small error-text" id="error_photo"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Standard</label>
                            <select name="iso_standard" class="form-control iso-standard-select">
                                <option value=""></option>
                                @foreach($isoOptions as $iso)
                                    <option value="{{ $iso }}">{{ $iso }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Pilih dari daftar atau ketik Standard baru</div>
                            <span class="text-danger small error-text" id="error_iso_standard"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dokumen</label>
                            <input type="file" name="document" class="form-control">
                            <div class="form-text">Semua jenis file, maks 5 MB</div>
                            <span class="text-danger small error-text" id="error_document"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">IA (Issues Date)</label>
                            <input type="date" name="issues_date" class="form-control">
                            <span class="text-danger small error-text" id="error_issues_date"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SA1</label>
                            <input type="date" name="sa1_date" class="form-control">
                            <span class="text-danger small error-text" id="error_sa1_date"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">SA2</label>
                            <input type="date" name="sa2_date" class="form-control">
                            <span class="text-danger small error-text" id="error_sa2_date"></span>
                        </div>
                        <div class="col-md-6 mb-3">
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
@foreach($allCertification as $row)
<div class="modal fade" id="updateCertificationModal{{ $row->id_certification }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <form class="updateCertificationForm" data-id="{{ $row->id_certification }}" action="{{ route('certification.update', $row->id_certification) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Edit Data Based Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                @php
                    // Format tanggal untuk input type="date"
                    $dateValue = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('Y-m-d') : '';
                @endphp

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client Name</label>
                            <select name="id_company" class="form-control">
                                <option value="">-- Select Client --</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id_company }}" {{ $row->id_company == $company->id_company ? 'selected' : '' }}>
                                        {{ $company->name_company }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger small error-text" id="error_id_company_{{ $row->id_certification }}"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sales Name</label>
                            <input type="text" name="sales_name" value="{{ $row->sales_name }}" class="form-control">
                            <span class="text-danger small error-text" id="error_sales_name_{{ $row->id_certification }}"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Scope</label>
                            <input type="text" name="scope" value="{{ $row->scope }}" class="form-control">
                            <span class="text-danger small error-text" id="error_scope_{{ $row->id_certification }}"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Legalitas</label>
                            <input type="text" name="legality" value="{{ $row->legality }}" class="form-control">
                            <span class="text-danger small error-text" id="error_legality_{{ $row->id_certification }}"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">PIC Name</label>
                            <input type="text" name="pic_name" value="{{ $row->pic_name }}" class="form-control">
                            <span class="text-danger small error-text" id="error_pic_name_{{ $row->id_certification }}"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Foto</label>
                            @if($row->photo_mime)
                                <div class="mb-2">
                                    <img src="{{ route('certification.photo', $row->id_certification) }}" width="100" class="img-thumbnail" loading="lazy" alt="Foto">
                                </div>
                            @endif
                            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">JPG, PNG, atau WEBP, maks 2 MB. Kosongkan jika tidak diganti</div>
                            <span class="text-danger small error-text" id="error_photo_{{ $row->id_certification }}"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Standard</label>
                            <select name="iso_standard" class="form-control iso-standard-select">
                                <option value=""></option>
                                @foreach($isoOptions as $iso)
                                    <option value="{{ $iso }}" {{ strcasecmp($iso, $row->type->type_certificate ?? '') === 0 ? 'selected' : '' }}>{{ $iso }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Pilih dari daftar atau ketik Standard baru</div>
                            <span class="text-danger small error-text" id="error_iso_standard_{{ $row->id_certification }}"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dokumen</label>
                            @if($row->document_name)
                                <div class="mb-2">
                                    <a href="{{ route('certification.document', $row->id_certification) }}" class="small">
                                        <i class="bi bi-download me-1"></i>{{ $row->document_name }}
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="document" class="form-control">
                            <div class="form-text">Semua jenis file, maks 5 MB. Kosongkan jika tidak diganti</div>
                            <span class="text-danger small error-text" id="error_document_{{ $row->id_certification }}"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">IA (Issues Date)</label>
                            <input type="date" name="issues_date" value="{{ $dateValue($row->issues_date) }}" class="form-control">
                            <span class="text-danger small error-text" id="error_issues_date_{{ $row->id_certification }}"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SA1</label>
                            <input type="date" name="sa1_date" value="{{ $dateValue($row->sa1_date) }}" class="form-control">
                            <span class="text-danger small error-text" id="error_sa1_date_{{ $row->id_certification }}"></span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">SA2</label>
                            <input type="date" name="sa2_date" value="{{ $dateValue($row->sa2_date) }}" class="form-control">
                            <span class="text-danger small error-text" id="error_sa2_date_{{ $row->id_certification }}"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">RC</label>
                            <input type="date" name="rc_date" value="{{ $dateValue($row->rc_date) }}" class="form-control">
                            <span class="text-danger small error-text" id="error_rc_date_{{ $row->id_certification }}"></span>
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
@foreach($allCertification as $row)
<div class="modal fade" id="certificationModal{{ $row->id_certification }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-sm">
            <!-- Header -->
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Certification Detail</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <!-- Company Info -->
                <div class="row mb-3 py-2 border-bottom" style="transition: background 0.3s;" onmouseover="this.style.background='#f8f9fa';" onmouseout="this.style.background='transparent';">
                    <div class="col-md-4 fw-bold">Company Name</div>
                    <div class="col-md-8">{{ $row->company->name_company }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom" style="transition: background 0.3s;" onmouseover="this.style.background='#f8f9fa';" onmouseout="this.style.background='transparent';">
                    <div class="col-md-4 fw-bold">Description</div>
                    <div class="col-md-8">{{ $row->company->description_company }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom" style="transition: background 0.3s;" onmouseover="this.style.background='#f8f9fa';" onmouseout="this.style.background='transparent';">
                    <div class="col-md-4 fw-bold">Phone</div>
                    <div class="col-md-8">{{ $row->company->phone_company }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom" style="transition: background 0.3s;" onmouseover="this.style.background='#f8f9fa';" onmouseout="this.style.background='transparent';">
                    <div class="col-md-4 fw-bold">Email</div>
                    <div class="col-md-8">{{ $row->company->email_company }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom" style="transition: background 0.3s;" onmouseover="this.style.background='#f8f9fa';" onmouseout="this.style.background='transparent';">
                    <div class="col-md-4 fw-bold">Address</div>
                    <div class="col-md-8">{{ $row->company->address_company }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom" onmouseout="this.style.background='transparent';">
                    <div class="col-md-4 fw-bold">Logo</div>
                    <div class="col-md-8">
                        @if($row->company->logo_company)
                            <img src="{{ Storage::url($row->company->logo_company) }}" width="50" class="img-thumbnail">
                        @else
                            <span class="text-muted">No Logo</span>
                        @endif
                    </div>
                </div>

                <!-- Certificate Info -->
                <div class="row mb-3 py-2 border-bottom" style="transition: background 0.3s;" onmouseover="this.style.background='#f8f9fa';" onmouseout="this.style.background='transparent';">
                    <div class="col-md-4 fw-bold">Type Certificate</div>
                    <div class="col-md-8">{{ $row->type->type_certificate }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom" style="transition: background 0.3s;" onmouseover="this.style.background='#f8f9fa';" onmouseout="this.style.background='transparent';">
                    <div class="col-md-4 fw-bold">Description Type Certificate</div>
                    <div class="col-md-8">{{ $row->type->description_certificate }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom" style="transition: background 0.3s;" onmouseover="this.style.background='#f8f9fa';" onmouseout="this.style.background='transparent';">
                    <div class="col-md-4 fw-bold">Issues Date (IA)</div>
                    <div class="col-md-8">{{ $row->issues_date_formatted }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom" style="transition: background 0.3s;" onmouseover="this.style.background='#f8f9fa';" onmouseout="this.style.background='transparent';">
                    <div class="col-md-4 fw-bold">Expired Date</div>
                    <div class="col-md-8">{{ $row->expired_date_formatted }}</div>
                </div>

                <!-- Data Based Client -->
                @php
                    $dateText = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d-m-Y') : null;
                @endphp
                @foreach([
                    'Sales Name' => $row->sales_name,
                    'Scope' => $row->scope,
                    'Legalitas' => $row->legality,
                    'PIC Name' => $row->pic_name,
                    'SA1' => $dateText($row->sa1_date),
                    'SA2' => $dateText($row->sa2_date),
                    'RC' => $dateText($row->rc_date),
                ] as $label => $value)
                <div class="row mb-3 py-2 border-bottom" style="transition: background 0.3s;" onmouseover="this.style.background='#f8f9fa';" onmouseout="this.style.background='transparent';">
                    <div class="col-md-4 fw-bold">{{ $label }}</div>
                    <div class="col-md-8">{{ $value ?: '-' }}</div>
                </div>
                @endforeach
                <div class="row mb-3 py-2 border-bottom">
                    <div class="col-md-4 fw-bold">Foto</div>
                    <div class="col-md-8">
                        @if($row->photo_mime)
                            <a href="{{ route('certification.photo', $row->id_certification) }}" target="_blank">
                                <img src="{{ route('certification.photo', $row->id_certification) }}" width="150" class="img-thumbnail" loading="lazy" alt="Foto">
                            </a>
                        @else
                            <span class="text-muted">No Foto</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3 py-2 border-bottom">
                    <div class="col-md-4 fw-bold">Dokumen</div>
                    <div class="col-md-8">
                        @if($row->document_name)
                            <a href="{{ route('certification.document', $row->id_certification) }}">
                                <i class="bi bi-download me-1"></i>{{ $row->document_name }}
                            </a>
                        @else
                            <span class="text-muted">No Dokumen</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3 py-2 border-bottom"
                    style="transition: background 0.3s;"
                    onmouseover="this.style.background='#f8f9fa';"
                    onmouseout="this.style.background='transparent';">
                    
                    <div class="col-md-4 fw-bold">QR Code</div>
                    <div class="col-md-8">
                        <img src="data:image/png;base64,{{ $row->qrcode }}">
                    <div class="mt-2">{{ $row->barcode_certificate }}</div>
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

    let table = $('#certificationList').DataTable({
        pageLength: 10
    });

    // Dropdown Standard selalu menempel tepat di bawah field. Bawaan Select2 (attachBody) menghitung
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
    let standardDropdown = Utils.Decorate(
        Utils.Decorate(Utils.Decorate(s2('select2/dropdown'), s2('select2/dropdown/search')), s2('select2/dropdown/closeOnSelect')),
        AttachBelowField
    );

    // Dropdown Standard: pilih dari daftar atau ketik Standard baru
    $('.iso-standard-select').each(function(){
        $(this).select2({
            theme: 'bootstrap-5',
            tags: true,
            width: '100%',
            placeholder: '-- Select or type Standard --',
            dropdownAdapter: standardDropdown
        }).on('select2:open', function(){
            // Pastikan daftar pilihan terlihat penuh di dalam modal (setelah hasil selesai di-render)
            let dropdown = $(this).next('.select2-container').find('.select2-dropdown')[0];
            setTimeout(function(){
                dropdown.scrollIntoView({ block: 'nearest' });
            });
        });
    });

    // Tombol Clear: hapus pesan error dan sinkronkan tampilan Select2 setelah form di-reset
    $('#createCertificationForm, .updateCertificationForm').on('reset', function(){
        let form = $(this);
        form.find('.error-text').text('');
        setTimeout(function(){
            form.find('.iso-standard-select').trigger('change');
        });
    });

    // ================= CREATE =================
    $('#createCertificationForm').submit(function(e){
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

                $('#createCertificationModal').modal('hide');
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
    $('.updateCertificationForm').submit(function(e){
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

                $('#updateCertificationModal'+id).modal('hide');

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
    $(document).on('click', '.deleteCertificationBtn', function(){

        let url = $(this).data('url');
        let id = $(this).data('id');
        let row = $('#certificationRow' + id);

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
