@extends('layouts.app')

@section('title', 'Type Certificate')

@section('content')
<style>
#typeCertificateList {
    table-layout: fixed;
    width: 100%;
}

#typeCertificateList td,
#typeCertificateList th{
    vertical-align: middle;
}

.wrap-text{
    white-space: normal !important;
    word-break: break-word;
}

.action-col{
    width:100px;
    white-space: nowrap;
}

.col-type{ width:20%; }
.col-desc{ width:30%; }
.col-scope{ width:30%; }
.col-no{ width:5%; }
</style>

<div class="main-content">
    <div class="card stretch stretch-full">
        <div class="d-flex justify-content-between align-items-center p-3">
            <h5 class="card-title mb-0">Standard</h5>
            <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#createTypeCertificateModal">
                <i class="bi bi-clipboard2-plus me-2"></i> Create
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover" id="typeCertificateList" style="table-layout: fixed; width:100%;">
                    <thead>
                        <tr>
                            <th class="col-no">No</th>
                            <th class="col-type">Standard</th>
                            <th class="col-desc">Description Standard</th>
                            <th class="col-scope">Scope Standard</th>
                            <th class="col-scope">Logo Standard</th>
                            <th class="action-col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allTypeCertificate as $key => $row)
                        <tr id="{{ $row->id_type_certificate }}">
                            
                            <td class="col-no">{{ $key + 1 }}</td>

                            <td class="wrap-text col-type">
                                {{ $row->type_certificate }}
                            </td>

                            <td class="wrap-text col-desc">
                                {{ $row->description_certificate }}
                            </td>

                            <td class="wrap-text col-scope">
                                {{ $row->scope_certificate }}
                            </td>
                            <td class="wrap-text col-scope">
                                @if($row->logo_certificate)
                                    <img src="{{ Storage::url($row->logo_certificate) }}" width="50" class="img-thumbnail">
                                @endif
                            </td>

                            <td class="action-col">
                                <x-action-dropdown>
                                    <li>
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                            data-bs-target="#updateTypeCertificateModal{{ $row->id_type_certificate }}">
                                            <i class="feather-edit me-2 text-warning"></i>Edit
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button type="button" class="dropdown-item text-danger deleteTypeCertificateBtn"
                                            data-url="{{ route('type-certificate.destroy', $row->id_type_certificate) }}"
                                            data-id="{{ $row->id_type_certificate }}">
                                            <i class="feather-trash-2 me-2"></i>Hapus
                                        </button>
                                    </li>
                                </x-action-dropdown>
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
<div class="modal fade" id="createTypeCertificateModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <form id="createTypeCertificateForm" action="{{ route('type-certificate.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Create Standard</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Left Column --}}
                        <div class="col-md-12">
                            <div class="mb-6">
                                <label class="form-label">Standard</label>
                                <input type="text" name="type_certificate" class="form-control">
                                <span class="text-danger small error-text" id="error_type_certificate"></span>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Description Standard</label>
                                <input type="text" name="description_certificate" class="form-control">
                                <span class="text-danger small error-text" id="error_description_cetificate"></span>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Scope Standard</label>
                                <input type="text" name="scope_certificate" class="form-control">
                                <span class="text-danger small error-text" id="error_scope_cetificate"></span>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Logo Standard</label>
                                <input type="file" name="logo_certificate" class="form-control" accept="image/*">
                                <span class="text-danger small error-text" id="error_logo_certificate"></span>
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
@foreach($allTypeCertificate as $row)
<div class="modal fade" id="updateTypeCertificateModal{{ $row->id_type_certificate }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <form class="updateTypeCertificateForm" data-id="{{ $row->id_type_certificate }}" action="{{ route('type-certificate.update', $row->id_type_certificate) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Edit Standard</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Left Column --}}
                        <div class="col-md-12">
                            <div class="mb-6">
                                <label class="form-label">Standard</label>
                                <input type="text" name="type_certificate" value="{{ $row->type_certificate }}" class="form-control">
                                <span class="text-danger small error-text" id="error_type_certificate_{{ $row->id_type_certificate }}"></span>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Description Standard</label>
                                <input type="text" name="description_certificate" value="{{ $row->description_certificate }}" class="form-control">
                                <span class="text-danger small error-text" id="error_description_certificate_{{ $row->id_type_certificate }}"></span>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Scope Standard</label>
                                <input type="text" name="scope_certificate" value="{{ $row->scope_certificate }}" class="form-control">
                                <span class="text-danger small error-text" id="error_scope_certificate_{{ $row->id_type_certificate }}"></span>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Logo Standard</label>
                                @if($row->logo_certificate)
                                    <div class="mb-2">
                                    <img src="{{ Storage::url($row->logo_certificate) }}" width="100" class="img-thumbnail">
                                    </div>
                                @endif
                                <input type="file" name="logo_certificate" class="form-control" accept="image/*">
                                <span class="text-danger small error-text" id="error_logo_certificate_{{ $row->id_type_certificate }}"></span>
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
{{-- @foreach($allTypeCertificate as $row)
<div class="modal fade" id="typeCertificateModal{{ $row->id_id_type_certificate }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Type Certificate Detail</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Type Certificate</div>
                    <div class="col-md-8">{{ $row->type_certificate }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Description Certificate</div>
                    <div class="col-md-8">{{ $row->description_certificate }}</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach --}}

@push('scripts')
<script>
$(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#typeCertificateList').DataTable({
        pageLength: 10
    });

    // ==========================
    // CREATE
    // ==========================
    $('#createTypeCertificateForm').on('submit', function (e) {

        e.preventDefault();

        let form = $(this);
        let formData = new FormData(this);

        form.find('.error-text').text('');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,

            success: function (res) {

                $('#createTypeCertificateModal').modal('hide');
                form[0].reset();

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: res.message,
                    timer: 1200,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });

            },

            error: function (err) {

                if (err.status === 422) {

                    $.each(err.responseJSON.errors, function (key, value) {
                        $('#error_' + key).text(value[0]);
                    });

                } else {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Server error occurred.'
                    });

                }

            }

        });

    });


    // ==========================
    // UPDATE
    // ==========================
    $('.updateTypeCertificateForm').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let id = form.data('id');

        let formData = new FormData(this);

        form.find('.error-text').text('');

        $.ajax({

            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,

            success: function (res) {

                $('#updateTypeCertificateModal' + id).modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: res.message,
                    timer: 1200,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });

            },

            error: function (err) {

                if (err.status === 422) {

                    $.each(err.responseJSON.errors, function (key, value) {

                        $('#error_' + key + '_' + id).text(value[0]);

                    });

                } else {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Server error occurred.'
                    });

                }

            }

        });

    });


    // ==========================
    // DELETE
    // ==========================
    $(document).on('click', '.deleteTypeCertificateBtn', function () {

        let url = $(this).data('url');

        Swal.fire({

            title: 'Are you sure?',
            text: 'This data will be deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'

        }).then((result) => {

            if (!result.isConfirmed) return;

            $.ajax({

                url: url,
                type: 'POST',
                data: {
                    _method: 'DELETE'
                },

                success: function (res) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: res.message,
                        timer: 1200,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });

                },

                error: function () {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Server error occurred.'
                    });

                }

            });

        });

    });

});
</script>
@endpush

@endsection
