@extends('layouts.app')

@section('title', 'Certification')

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

/* Badge status sertifikat: Active (hijau), Suspended (oranye), Withdrawn (merah) */
.cert-status{
    display: inline-block;
    padding: 4px 10px;
    border-radius: 50rem;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}
.cert-status-active{
    background-color: #dcfce7;
    color: #166534;
}
.cert-status-suspended{
    background-color: #fef3c7;
    color: #92400e;
}
.cert-status-withdrawn{
    background-color: #fee2e2;
    color: #991b1b;
}
html.app-skin-dark .cert-status-active{
    background-color: rgba(34, 197, 94, 0.16);
    color: #86efac;
}
html.app-skin-dark .cert-status-suspended{
    background-color: rgba(245, 158, 11, 0.16);
    color: #fcd34d;
}
html.app-skin-dark .cert-status-withdrawn{
    background-color: rgba(239, 68, 68, 0.16);
    color: #fca5a5;
}
</style>

<div class="main-content">
    <div class="card stretch stretch-full">
        <div class="d-flex justify-content-between align-items-center p-3">
            <h5 class="card-title page-title mb-0">Certification</h5>
            <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#createCertificationModal">
                <i class="feather-plus me-1"></i> Create
            </button>
        </div>

        <x-list-toolbar table="certificationList" :export="route('certification.export')"
            :dates="[4 => 'Issues Date', 5 => 'Expired Date']"
            :sorts="['4:desc' => 'Issues Date terbaru', '4:asc' => 'Issues Date terlama', '5:asc' => 'Expired Date terdekat', '5:desc' => 'Expired Date terjauh']"
            :status="['column' => 6, 'options' => array_keys(\App\Models\Certification::STATUSES), 'badge' => 'cert-status cert-status-']" />

        <div class="card-body p-0">
   <div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-list table-hover table-borderless align-middle mb-0"
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
                    <th>Status</th>
                    <th>Note</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($allCertification as $key => $row)
                <tr id="certificationRow{{ $row->id_certification }}" data-id="{{ $row->id_certification }}">

                    <td class="text-center">{{ $key + 1 }}</td>

                    <td>{{ $row->company->name_company }}</td>

                    <td>{{ $row->type->type_certificate }}</td>

                    <td>{{ $row->number_certificate }}</td>

                    <td class="text-center" data-order="{{ $row->issues_date }}">{{ $row->issues_date_formatted }}</td>

                    <td class="text-center" data-order="{{ $row->expired_date }}">{{ $row->expired_date_formatted }}</td>

                    <td class="text-center" data-search="{{ $row->status }}">
                        <span class="cert-status cert-status-{{ strtolower($row->status) }}">{{ $row->status }}</span>
                    </td>

                    <td class="note-cell">
                        @if($row->note)
                            <span title="{{ $row->note }}">{{ $row->note }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    <td>
                        <div class="d-flex align-items-center gap-1 flex-nowrap">
                            <x-action-dropdown>
                                <li>
                                    <a href="{{ route('certification.generatePdf', $row->id_certification) }}"
                                        class="dropdown-item" target="_blank">
                                        <i class="bi bi-file-earmark-pdf-fill me-2 text-danger"></i>Generate PDF Final
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('certification.generatePdfDraft', $row->id_certification) }}"
                                        class="dropdown-item" target="_blank">
                                        <i class="bi bi-file-earmark-text me-2 text-warning"></i>Generate PDF Draft
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
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
                    <h5 class="modal-title">Create Certification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Left Column --}}
                        <div class="col-md-12">
                            <div class="mb-6">
                                <label class="form-label">Company</label>
                                <select name="id_company" class="form-control">
                                    <option value="">-- Select Company --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id_company }}">{{ $company->name_company }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger small error-text" id="error_id_company"></span>
                            </div>

                            <div class="mb-6">
                                <label class="form-label">Type Certificate</label>
                                <select name="id_type_certificate" class="form-control">
                                    <option value="">-- Select Type Certificate --</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id_type_certificate }}">{{ $type->type_certificate }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger small error-text" id="error_id_type_certificate"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Issues Date</label>
                                <input type="date" name="issues_date" class="form-control">
                                <span class="text-danger small error-text" id="error_issues_date"></span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <textarea name="note" rows="3" class="form-control" placeholder="Catatan (opsional)"></textarea>
                                <span class="text-danger small error-text" id="error_note"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
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
                    <h5 class="modal-title">Edit Certification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            {{-- Company --}}
                            <div class="mb-3">
                                <label class="form-label">Company</label>
                                <select name="id_company" class="form-control">
                                    <option value="">-- Select Company --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id_company }}" {{ $row->id_company == $company->id_company ? 'selected' : '' }}>
                                            {{ $company->name_company }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger small error-text" id="error_id_company_{{ $row->id_certification }}"></span>
                            </div>

                            {{-- Type Certificate --}}
                            <div class="mb-3">
                                <label class="form-label">Type Certificate</label>
                                <select name="id_type_certificate" class="form-control">
                                    <option value="">-- Select Type Certificate --</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id_type_certificate }}" {{ $row->id_type_certificate == $type->id_type_certificate ? 'selected' : '' }}>
                                            {{ $type->type_certificate }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger small error-text" id="error_id_type_certificate_{{ $row->id_certification }}"></span>
                            </div>

                            {{-- Issues Date --}}
                            <div class="mb-3">
                                <label class="form-label">Issues Date</label>
                                <input type="date" name="issues_date" value="{{ $row->issues_date }}" class="form-control">
                                <span class="text-danger small error-text" id="error_issues_date_{{ $row->id_certification }}"></span>
                            </div>

                            {{-- Note --}}
                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <textarea name="note" rows="3" class="form-control" placeholder="Catatan (opsional)">{{ $row->note }}</textarea>
                                <span class="text-danger small error-text" id="error_note_{{ $row->id_certification }}"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
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
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Company Name</div>
                    <div class="col-md-8">{{ $row->company->name_company }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Description</div>
                    <div class="col-md-8">{{ $row->company->description_company }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Phone</div>
                    <div class="col-md-8">{{ $row->company->phone_company }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Email</div>
                    <div class="col-md-8">{{ $row->company->email_company }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Address</div>
                    <div class="col-md-8">{{ $row->company->address_company }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom detail-row">
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
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Type Certificate</div>
                    <div class="col-md-8">{{ $row->type->type_certificate }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Description Type Certificate</div>
                    <div class="col-md-8">{{ $row->type->description_certificate }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Issues Date</div>
                    <div class="col-md-8">{{ $row->issues_date_formatted }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Expired Date</div>
                    <div class="col-md-8">{{ $row->expired_date_formatted }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Status</div>
                    <div class="col-md-8">
                        <span class="cert-status cert-status-{{ strtolower($row->status) }}">{{ $row->status }}</span>
                    </div>
                </div>
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Note</div>
                    <div class="col-md-8" style="white-space: pre-line;">{{ $row->note ?: '-' }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom detail-row">
                    
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

    // Default urut Issues Date terbaru; kolom No & Action tidak bisa diurutkan
    let table = $('#certificationList').DataTable({
        pageLength: 10,
        order: [[4, 'desc']],
        columnDefs: [{ targets: [0, -1], orderable: false, searchable: false }]
    });
    setupListToolbar(table);

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

                // Reload agar modal detail/edit untuk data baru ikut ter-render
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

                // Reload agar modal detail ikut ter-update
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

                        // Nomor urut diperbarui otomatis saat tabel di-draw ulang
                        table.row(row).remove().draw(false);

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
