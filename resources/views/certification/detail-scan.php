<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Certification - {{ $certification->number_certificate }}</title>
    <link rel="icon" href="{{ asset($certification->company->logo_company) }}" type="image/png">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 40px;
        }
        .company-logo {
            width: 100px;
        }
        .info {
            margin-top: 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h3>Certification Detail</h3>

    <hr>

    <h5>Company</h5>
    <p>Name: {{ $certification->company->name_company }}</p>

    <h5>Type Certificate</h5>
    <p>Type: {{ $certification->type->type_certificate }}</p>
    <p>Description: {{ $certification->type->description_certificate }}</p>

    <h5>Certification</h5>
    <p>Number: {{ $certification->number_certificate }}</p>
    <p>Issue Date: {{ $certification->issues_date }}</p>
    <p>Expired Date: {{ $certification->expired_date }}</p>
</body>
</html>