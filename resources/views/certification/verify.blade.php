<!DOCTYPE html>
<html>
<head>
    <title>Certificate Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow-lg border-0">
        <div class="card-body">

            <h3 class="text-center mb-4 text-success">Certificate Verified ✅</h3>

            <hr>

            <h5>Company Information</h5>
            <p><strong>Name:</strong> {{ $certification->company->name_company }}</p>
            <p><strong>Description:</strong> {{ $certification->company->description_company }}</p>
            <p><strong>Address:</strong> {{ $certification->company->address_company }}</p>
            <p><strong>Phone:</strong> {{ $certification->company->phone_company }}</p>
            <p><strong>Email:</strong> {{ $certification->company->email_company }}</p>

            <hr>

            <h5>Type Certificate</h5>
            <p><strong>Type:</strong> {{ $certification->type->type_certificate }}</p>
            <p><strong>Description:</strong> {{ $certification->type->description_certificate }}</p>

            <hr>

            <h5>Certification Details</h5>
            <p><strong>Certificate Number:</strong> {{ $certification->number_certificate }}</p>
            <p><strong>Issue Date:</strong> {{ $certification->issues_date }}</p>
            <p><strong>Expired Date:</strong> {{ $certification->expired_date }}</p>

            @if(\Carbon\Carbon::parse($certification->expired_date)->isPast())
                <div class="alert alert-danger mt-3">
                    Status: EXPIRED
                </div>
            @else
                <div class="alert alert-success mt-3">
                    Status: ACTIVE
                </div>
            @endif

        </div>
    </div>
</div>

</body>
</html>