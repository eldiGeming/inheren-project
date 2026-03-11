@extends('layouts.app')

@section('title', 'Type Certificate')

@section('content')
<div class="main-content">
    <div class="card stretch stretch-full">
        <div class="d-flex justify-content-end p-3">
            <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#createTypeCertificateModal">
                <i class="bi bi-clipboard2-plus me-2"></i> Create
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover" id="typeCertificateList">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Type Certificate</th>
                            <th>Description Certificate</th>
                            <th>Scope Certificate</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allTypeCertificate as $key => $row)
                        <tr id="typeCertificateRow{{ $row->id_type_certificate }}">
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $row->type_certificate }}</td>
                            <td>{{ $row->description_certificate }}</td>
                            <td>{{ $row->scope_certificate }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#updateTypeCertificateModal{{ $row->id_type_certificate }}">
                                        <i class="feather-edit me-1"></i> Edit
                                    </button>
                                    <button 
                                        type="button"
                                        class="btn btn-sm btn-danger deleteTypeCertificateBtn"
                                        data-url="{{ route('type-certificate.destroy', $row->id_type_certificate) }}"
                                        data-id="{{ $row->id_type_certificate }}">
                                        <i class="feather-trash-2 me-1"></i> Delete
                                    </button>
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
<div class="modal fade" id="createTypeCertificateModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <form id="createTypeCertificateForm" action="{{ route('type-certificate.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Create Type Certificate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Left Column --}}
                        <div class="col-md-12">
                            <div class="mb-6">
                                <label class="form-label">Type Certificate</label>
                                <input type="text" name="type_certificate" class="form-control">
                                <span class="text-danger small error-text" id="error_type_certificate"></span>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Description Certificate</label>
                                <input type="text" name="description_certificate" class="form-control">
                                <span class="text-danger small error-text" id="error_description_cetificate"></span>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Scope Certificate</label>
                                <input type="text" name="scope_certificate" class="form-control">
                                <span class="text-danger small error-text" id="error_scope_cetificate"></span>
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
                    <h5 class="modal-title">Edit Type Certification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Left Column --}}
                        <div class="col-md-12">
                            <div class="mb-6">
                                <label class="form-label">Type Certificate</label>
                                <input type="text" name="type_certificate" value="{{ $row->type_certificate }}" class="form-control">
                                <span class="text-danger small error-text" id="error_type_certificate_{{ $row->id_type_certificate }}"></span>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Description Certificate</label>
                                <input type="text" name="description_certificate" value="{{ $row->description_certificate }}" class="form-control">
                                <span class="text-danger small error-text" id="error_description_certificate_{{ $row->id_type_certificate }}"></span>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Scope Certificate</label>
                                <input type="text" name="scope_certificate" value="{{ $row->scope_certificate }}" class="form-control">
                                <span class="text-danger small error-text" id="error_scope_certificate_{{ $row->id_type_certificate }}"></span>
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
$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let table = $('#typeCertificateList').DataTable({
        pageLength: 10
    });

    $('#createTypeCertificateForm').submit(function(e){
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

            $('#createTypeCertificateModal').modal('hide');
            form[0].reset();

            let data = res.data;

            let actionButtons = `
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-warning"
                        data-bs-toggle="modal"
                        data-bs-target="#updateTypeCertificateModal${data.id}">
                        <i class="feather-edit me-1"></i> Edit
                    </button>

                    <button type="button"
                        class="btn btn-sm btn-danger deleteTypeCertificateBtn"
                        data-url="/type-certificate/${data.id}"
                        data-id="${data.id}">
                        <i class="feather-trash-2 me-1"></i> Delete
                    </button>
                </div>
            `;

            let rowNode = table.row.add([
                table.rows().count() + 1,
                data.type_certificate,
                data.description_certificate,
                data.scope_certificate,
                actionButtons
            ]).draw(false).node();

            $(rowNode).attr('id', 'typeCertificateRow' + data.id);

            Swal.fire({
                icon: 'success',
                title: 'Created!',
                text: res.message,
                timer: 1500,
                showConfirmButton: false
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

    $('.updateTypeCertificateForm').submit(function(e){
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

                $('#updateTypeCertificateModal'+id).modal('hide');

                let row = $('#typeCertificateRow'+id);
                let rowData = table.row(row).data();

                // Update kolom langsung
                table.row(row).data([
                    rowData[0],
                    res.data.type_certificate,
                    res.data.description_certificate,
                    res.data.scope_certificate,
                    rowData[4]
                ]).draw(false);

                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
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


    $(document).on('click', '.deleteTypeCertificateBtn', function(){

        let url = $(this).data('url');
        let id = $(this).data('id');
        let row = $('#typeCertificateRow' + id);

        Swal.fire({
            title: 'Are you sure?',
            text: "This data will be deleted.!",
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

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Re-numbering kolom No
                        table.rows().every(function(index){
                            this.cell(index,0).data(index+1);
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
