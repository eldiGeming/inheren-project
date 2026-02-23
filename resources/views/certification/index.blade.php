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
            <div class="table-responsive">
                <table class="table table-hover" id="certificationList">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Company Name</th>
                            <th>Type Certificate</th>
                            <th>Number Certicate</th>
                            <th>Issues Date</th>
                            <th>Expired Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allCertification as $key => $row)
                        <tr id="certificationRow{{ $row->id_certification }}">
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $row->company->name_company }}</td>
                            <td>{{ $row->type->type_certificate }}</td>
                            <td>{{ $row->number_certificate }}</td>
                            <td>{{ $row->issues_date }}</td>
                            <td>{{ $row->expired_date }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal"
                                        data-bs-target="#certificationModal{{ $row->id_certification }}">
                                        <i class="feather-eye me-1"></i> Detail
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#updateCertificationModal{{ $row->id_certification }}">
                                        <i class="feather-edit me-1"></i> Edit
                                    </button>
                                    <button 
                                        type="button"
                                        class="btn btn-sm btn-danger deleteCertificationBtn"
                                        data-url="{{ route('certification.destroy', $row->id_certification) }}"
                                        data-id="{{ $row->id_certification }}">
                                        <i class="feather-trash-2 me-1"></i> Delete
                                    </button>    
                                        <a href="{{ route('certification.generatePdf', $row->id_certification) }}"
                                        class="btn btn-sm btn-secondary"
                                        target="_blank">
                                            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                                        </a>        
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
                    <div class="col-md-4 fw-bold">Issues Date</div>
                    <div class="col-md-8">{{ $row->issues_date }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom" style="transition: background 0.3s;" onmouseover="this.style.background='#f8f9fa';" onmouseout="this.style.background='transparent';">
                    <div class="col-md-4 fw-bold">Expired Date</div>
                    <div class="col-md-8">{{ $row->expired_date }}</div>
                </div>
                <div class="row mb-3 py-2 border-bottom"
                    style="transition: background 0.3s;"
                    onmouseover="this.style.background='#f8f9fa';"
                    onmouseout="this.style.background='transparent';">
                    
                    <div class="col-md-4 fw-bold">QR Code</div>
                    <div class="col-md-8">
                        {!! QrCode::size(150)->generate(route('certification.verify', $row->barcode_certificate)) !!}
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

            let data = res.data; // Certification data

            let actionButtons = `
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal"
                        data-bs-target="#certificationModal${data.id_certification}">
                        <i class="feather-eye me-1"></i> Detail
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                        data-bs-target="#updateCertificationModal${data.id_certification}">
                        <i class="feather-edit me-1"></i> Edit
                    </button>
                    <button type="button" class="btn btn-sm btn-danger deleteCertificationBtn"
                        data-url="/certification/${data.id_certification}"
                        data-id="${data.id_certification}">
                        <i class="feather-trash-2 me-1"></i> Delete
                    </button>                                   
                </div>
            `;

            let rowNode = table.row.add([
                table.rows().count() + 1,
                data.company ? data.company.name_company : '',
                data.type ? data.type.type_certificate : '',
                data.number_certificate,
                data.issues_date,
                data.expired_date,
                actionButtons
            ]).draw(false).node();

            $(rowNode).attr('id', 'certificationRow' + data.id_certification);

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


    $('.updateCertificationForm').submit(function(e){
        e.preventDefault();

        let form = $(this);
        let id = form.data('id');
        let formData = new FormData(this);
        form.find('.error-text').text('');
        console.log("data",formData);
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){

                $('#updateCertificationModal'+id).modal('hide');

                let row = $('#certificationRow'+id);

                table.row(row).data([
                    table.row(row).index() + 1,
                    res.data.company ? res.data.company.name_company : '',
                    res.data.type ? res.data.type.type_certificate : '',
                    res.data.number_certificate,
                    res.data.issues_date,
                    res.data.expired_date,
                    row.find('td:eq(6)').html()
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


    $(document).on('click', '.deleteCertificationBtn', function(){

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
