@extends('layouts.app')

@section('title', 'Company')

@section('content')
<div class="main-content">
    <div class="card stretch stretch-full">
        <div class="d-flex justify-content-end p-3">
            <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#createCompanyModal">
                <i class="bi bi-building-add me-2"></i> Create
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover" id="companyList">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Company Name</th>
                            <th>Company Phone</th>
                            <th>Company Email</th>
                            <th>Company Logo</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allCompany as $key => $row)
                        <tr id="companyRow{{ $row->id_company }}">
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $row->name_company }}</td>
                            <td>{{ $row->phone_company }}</td>
                            <td>{{ $row->email_company }}</td>
                            <td>
                                @if($row->logo_company)
                                    <img src="{{ Storage::url($row->logo_company) }}" width="50" class="img-thumbnail">
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal"
                                        data-bs-target="#companyModal{{ $row->id_company }}">
                                        <i class="feather-eye me-1"></i> Detail
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#updateCompanyModal{{ $row->id_company }}">
                                        <i class="feather-edit me-1"></i> Edit
                                    </button>
                                    <button 
                                        type="button"
                                        class="btn btn-sm btn-danger deleteCompanyBtn"
                                        data-url="{{ route('company.destroy', $row->id_company) }}"
                                        data-id="{{ $row->id_company }}">
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
<div class="modal fade" id="createCompanyModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <form id="createCompanyForm" action="{{ route('company.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Create Company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Left Column --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="name_company" class="form-control">
                                <span class="text-danger small error-text" id="error_name_company"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <input type="text" name="description_company" class="form-control">
                                <span class="text-danger small error-text" id="error_description_company"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone_company" class="form-control">
                                <span class="text-danger small error-text" id="error_phone_company"></span>
                            </div>
                        </div>

                        {{-- Right Column --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email_company" class="form-control">
                                <span class="text-danger small error-text" id="error_email_company"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="address_company" class="form-control">
                                <span class="text-danger small error-text" id="error_address_company"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Logo</label>
                                <input type="file" name="logo_company" class="form-control" accept="image/*">
                                <span class="text-danger small error-text" id="error_logo_company"></span>
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
@foreach($allCompany as $row)
<div class="modal fade" id="updateCompanyModal{{ $row->id_company }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <form class="updateCompanyForm" data-id="{{ $row->id_company }}" action="{{ route('company.update', $row->id_company) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Edit Company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Left Column --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="name_company" value="{{ $row->name_company }}" class="form-control">
                                <span class="text-danger small error-text" id="error_name_company_{{ $row->id_company }}"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <input type="text" name="description_company" value="{{ $row->description_company }}" class="form-control">
                                <span class="text-danger small error-text" id="error_description_company_{{ $row->id_company }}"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone_company" value="{{ $row->phone_company }}" class="form-control">
                                <span class="text-danger small error-text" id="error_phone_company_{{ $row->id_company }}"></span>
                            </div>
                        </div>

                        {{-- Right Column --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email_company" value="{{ $row->email_company }}" class="form-control">
                                <span class="text-danger small error-text" id="error_email_company_{{ $row->id_company }}"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="address_company" value="{{ $row->address_company }}" class="form-control">
                                <span class="text-danger small error-text" id="error_address_company_{{ $row->id_company }}"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Logo</label>
                                @if($row->logo_company)
                                    <div class="mb-2">
                                    <img src="{{ Storage::url($row->logo_company) }}" width="50" class="img-thumbnail">
                                    </div>
                                @endif
                                <input type="file" name="logo_company" class="form-control" accept="image/*">
                                <span class="text-danger small error-text" id="error_logo_company_{{ $row->id_company }}"></span>
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
@foreach($allCompany as $row)
<div class="modal fade" id="companyModal{{ $row->id_company }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Company Detail</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Name</div>
                    <div class="col-md-8">{{ $row->name_company }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Description</div>
                    <div class="col-md-8">{{ $row->description_company }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Phone</div>
                    <div class="col-md-8">{{ $row->phone_company }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Email</div>
                    <div class="col-md-8">{{ $row->email_company }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Address</div>
                    <div class="col-md-8">{{ $row->address_company }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Logo</div>
                    <div class="col-md-8">
                        @if($row->logo_company)
                            <img src="{{ Storage::url($row->logo_company) }}" width="50" class="img-thumbnail">
                        @endif
                    </div>
                </div>
            </div>
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

    let table = $('#companyList').DataTable({
        pageLength: 10
    });

    $('#createCompanyForm').submit(function(e){
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

                $('#createCompanyModal').modal('hide');
                form[0].reset();
                let data = res.data;
                let actionButtons = `
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal"
                            data-bs-target="#companyModal{{ $row->id_company }}">
                            <i class="feather-eye me-1"></i> Detail
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                            data-bs-target="#updateCompanyModal{{ $row->id_company }}">
                            <i class="feather-edit me-1"></i> Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-danger deleteCompanyBtn"
                            data-url="/company/${data.id}"
                            data-id="${data.id}">
                            <i class="feather-trash-2 me-1"></i> Delete
                        </button>                                   
                    </div>
                `;

                let rowNode = table.row.add([
                    table.rows().count() + 1,
                    data.name_company,
                    data.phone_company,
                    data.email_company,
                    data.logo_company 
                        ? `<img src="/storage/${data.logo_company}" width="50" class="img-thumbnail">`
                        : '',
                    actionButtons
                ]).draw(false).node();

                $(rowNode).attr('id', 'companyRow' + data.id);

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


    $('.updateCompanyForm').submit(function(e){
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
                console.log("data", res.data);
                $('#updateCompanyModal'+id).modal('hide');

                let row = $('#companyRow'+id);

                table.row(row).data([
                    table.row(row).index() + 1,
                    res.data.name_company,
                    res.data.phone_company,
                    res.data.email_company,
                    res.data.logo_company ? `<img src="/storage/${res.data.logo_company}" width="50" class="img-thumbnail">`
                        : '',
                    row.find('td:eq(5)').html()
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


    $(document).on('click', '.deleteCompanyBtn', function(){

        let url = $(this).data('url');
        let id = $(this).data('id');
        let row = $('#companyRow' + id);

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
