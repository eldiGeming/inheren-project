<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 12px;
        }
        .status-badge {
            font-weight: bold;
            font-size: 1rem;
            padding: 0.75rem 0; /* lebih tinggi supaya terlihat menonjol */
            border-radius: 8px; /* lebih halus dari 50px */
            display: inline-block;
        }
        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .status-expired {
            background-color: #f8d7da;
            color: #842029;
        }
        h3 {
            font-weight: 700;
        }
        hr {
            border-top: 1px solid #dee2e6;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        .info-value {
            color: #212529;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="card shadow-lg border-0">
        <div class="card-body p-4">

            <h3 class="text-center mb-4 text-primary">Certificate Information</h3>

                @if(\Carbon\Carbon::parse($certification->expired_date)->isPast())
                    <div class="d-flex justify-content-center mb-4">
                        <span class="status-badge status-expired w-100 text-center">
                            EXPIRED
                        </span>
                    </div>
                @else   
                    <div class="d-flex justify-content-center mb-4">
                        <span class="status-badge status-active w-100 text-center">
                            ACTIVE
                        </span>
                    </div>
                @endif

            <hr>

            <h5 class="text-secondary mb-3">Company Information</h5>
            <p><span class="info-label">Name:</span> <span class="info-value">{{ $certification->company->name_company }}</span></p>
            <p><span class="info-label">Address:</span> <span class="info-value">{{ $certification->company->address_company }}</span></p>

            <hr>

            <h5 class="text-secondary mb-3">Type Certificate</h5>
            <p><span class="info-label">Type:</span> <span class="info-value">{{ $certification->type->type_certificate }}</span></p>
            <p><span class="info-label">Scope:</span> <span class="info-value">{{ $certification->type->scope_certificate }}</span></p>

            <hr>

            <h5 class="text-secondary mb-3">Certification Details</h5>
            <p><span class="info-label">Certificate Number:</span> <span class="info-value">{{ $certification->number_certificate }}</span></p>
            <p><span class="info-label">Issue Date:</span> <span class="info-value">{{ $certification->issues_date }}</span></p>
            <p><span class="info-label">Expired Date:</span> <span class="info-value">{{ $certification->expired_date }}</span></p>

        </div>
    </div>
</div>

</body>
</html>