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
            background-color: #fafafa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            min-height: 100vh;
        }

        /* ===== Top section wrapper (holds the watermark so it scrolls with this section only) ===== */
        .main-section {
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }

        /* ===== Watermark logo (scrolls together with the top section) ===== */
        .bg-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60%;
            max-width: 520px;
            opacity: 0.06;
            pointer-events: none;
            z-index: 0;
            user-select: none;
        }

        .content-wrapper {
            position: relative;
            z-index: 1;
        }

        @media (max-width: 480px) {
            .bg-watermark {
                width: 85%;
            }
        }

        .content-wrapper {
            max-width: 640px;
            margin: 0 auto;
            padding: 4rem 1.5rem 3rem 1.5rem;
        }

        /* ===== Status icon (verified / locked) ===== */
        .certificate-logo {
            width: 130px;   /* sesuaikan dengan ukuran icon lama */
            height: 130px;
            object-fit: contain;
        }

        .status-icon-wrapper {
            position: relative;
            width: 130px;
            height: 130px;
            margin: 0 auto 1.5rem auto;
        }

        .status-icon-wrapper .main-icon {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            position: relative;
        }


        .status-badge {
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: 0.06em;
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-align: center;
            width: 100%;
        }

        .status-badge i {
            font-size: 1rem;
        }

        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
            box-shadow: 0 2px 8px rgba(15, 81, 50, 0.15);
        }

        .status-expired {
            background-color: #f8d7da;
            color: #842029;
            box-shadow: 0 2px 8px rgba(132, 32, 41, 0.15);
        }

                .company-info {
            background: #ffffff;
            border: 1px solid #dcdfe3;
            border-radius: 4px;
            padding: 1.5rem 1.75rem;
            margin: 0 auto 1.75rem auto;
            max-width: 480px;
            text-align: left;
        }

        .company-info .company-name {
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .company-info .company-name .icon-badge {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            border: 1px solid #1b2a47;
            border-radius: 2px;
            color: #1b2a47;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            margin-top: 0.1rem;
        }

        .company-info .company-name .text-block {
            display: flex;
            flex-direction: column;
        }

        .company-info .company-name .label-eyebrow {
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 0.3rem;
        }

        .company-info .company-name .company-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #1b2a47;
            line-height: 1.35;
        }

        .company-info .company-address {
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
        }

        .company-info .company-address .icon-badge {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            border: 1px solid #adb5bd;
            border-radius: 2px;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .company-info .company-address .text-block {
            display: flex;
            flex-direction: column;
            padding-top: 0.05rem;
        }

        .company-info .company-address .label-eyebrow {
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 0.3rem;
        }

        .company-info .company-address .address-text {
            color: #495057;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        @media (max-width: 480px) {
            .company-info {
                padding: 1.25rem 1.35rem;
                max-width: 100%;
            }

            .company-info .company-name .company-title {
                font-size: 1rem;
            }

            .company-info .company-address .address-text {
                font-size: 0.85rem;
            }
        }

        h3 {
            font-weight: 700;
        }

        hr {
            border-top: 1px solid #dee2e6;
        }

        /* ===== Aligned label/value grid ===== */
        .info-grid {
            display: grid;
            grid-template-columns: 160px 1fr;
            row-gap: 0.85rem;
            column-gap: 0.75rem;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            text-align: left;
        }

        .info-value {
            color: #212529;
            text-align: left;
            word-break: break-word;
        }

        @media (max-width: 480px) {
            .info-grid {
                grid-template-columns: 130px 1fr;
            }

            .status-icon-wrapper {
                width: 100px;
                height: 100px;
            }

            .status-icon-wrapper .main-icon {
                width: 100px;
                height: 100px;
                font-size: 3rem;
            }

            .status-icon-wrapper .lock-overlay {
                width: 34px;
                height: 34px;
                font-size: 1rem;
            }
        }

        /* ===== Fixed WhatsApp button (does not scroll) ===== */
        .whatsapp-float {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 58px;
            height: 58px;
            background-color: #25d366;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            z-index: 1050;
            transition: transform 0.2s ease;
        }

        .whatsapp-float:hover {
            transform: scale(1.08);
            color: #fff;
        }

        @media (max-width: 480px) {
            .whatsapp-float {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
                bottom: 16px;
                right: 16px;
            }
        }

        /* ===== Footer ===== */
        .site-footer {
            background: linear-gradient(180deg, #10192e 0%, #1b2a47 55%, #101a30 100%);
            color: #cfd7e6;
            border-top: 4px solid #2a6f57;
        }

        .footer-logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            display: block;
            margin: 0 auto 1.25rem auto;
            padding: 10px;
        }

        .footer-about {
            max-width: 680px;
            margin: 0 auto;
            text-align: center;
            color: #b7c1d6;
            line-height: 1.7;
        }

        .section {
            padding: 4rem 0;
        }

        .section-gray {
            background-color: rgba(255, 255, 255, 0.03);
        }

        .section-title h2 {
            font-weight: 700;
            color: #ffffff;
        }

        .section-title p {
            color: #8b96ad;
        }

        .card-clean {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1.75rem;
            height: 100%;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        .card-clean p {
            margin-bottom: 1rem;
            color: #c4cce0;
        }

        .card-clean p strong {
            color: #ffffff;
        }

        .card-clean p:last-child {
            margin-bottom: 0;
        }

        .footer-copyright {
            text-align: center;
            padding: 1.25rem 0;
            font-size: 0.875rem;
            color: #8b96ad;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background-color: #0c1424;
        }
    </style>
</head>
<body>

<div class="main-section">

    {{-- <img src="{{ asset('assets/images/logo-abbr.png') }}" alt="" class="bg-watermark"> --}}

    @php
        $isExpired = \Carbon\Carbon::parse($certification->expired_date)->isPast();
    @endphp

    <div class="content-wrapper">

        <div class="status-icon-wrapper {{ $isExpired ? 'expired' : 'active' }}">
            <div class="main-icon">
                <img src="{{ asset('storage/' . $certification->type->logo_certificate) }}" 
                    alt="{{ $certification->type->type_certificate }}" 
                    class="certificate-logo">
            </div>
        </div>

        <div class="d-flex justify-content-center mb-3">
            @if($isExpired)
                <span class="status-badge status-expired">
                    <i class="bi bi-x-octagon-fill"></i> EXPIRED
                </span>
            @else
                <span class="status-badge status-active">
                    <i class="bi bi-patch-check-fill"></i> ACTIVE
                </span>
            @endif
        </div>

       <div class="company-info">
            <div class="company-name">
                <span class="icon-badge">
                    <i class="bi bi-building"></i>
                </span>
                <span class="text-block">
                    {{-- <span class="label-eyebrow">Certified Organization</span> --}}
                    <span class="company-title">{{ $certification->company->name_company }}</span>
                </span>
            </div>

            <div class="company-address">
                <span class="icon-badge">
                    <i class="bi bi-geo-alt"></i>
                </span>
                <span class="text-block">
                    {{-- <span class="label-eyebrow">Registered Address</span> --}}
                    <span class="address-text">{{ $certification->company->address_company }}</span>
                </span>
            </div>
        </div>

        <hr>

        <div class="info-grid">
            <div class="info-label">Certificate Number</div>
            <div class="info-value">{{ $certification->number_certificate }}</div>

            <div class="info-label">Standard</div>
            <div class="info-value">{{ $certification->type->type_certificate }}</div>

            <div class="info-label">Description</div>
            <div class="info-value">{{ $certification->type->description_certificate }}</div>

            <div class="info-label">Scope of Activities</div>
            <div class="info-value">{{ $certification->type->scope_certificate }}</div>

            <div class="info-label">Issue Date</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($certification->issues_date)->format('d F Y') }}</div>

            <div class="info-label">Expired Date</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($certification->expired_date)->format('d F Y') }}</div>
        </div>

    </div>

</div>

{{-- ============ FOOTER (separate section, logo is not part of this) ============ --}}
<footer class="site-footer">

    <div class="section">
        <div class="container">

            <img src="{{ asset('assets/images/logo-abbr.png') }}" alt="Inheren Certification" class="footer-logo">

            <p class="footer-about">
                Inheren Certification is a professional certification body committed to helping organizations
                achieve internationally recognized ISO standards. We deliver structured, transparent, and
                efficient certification processes designed to strengthen credibility and business performance.
            </p>

        </div>
    </div>

    <section id="contact" class="section section-gray">
        <div class="container">

            <div class="section-title text-center mb-5">
                <h2>Contact Us</h2>
                <p>Start Your Certification Journey Today</p>
            </div>

            <div class="row g-4">

                <div class="col-md-6">
                    <div class="card-clean">
                        <p><strong>Address:</strong><br> PLAZA MUTIARA Mega Kuningan Lantai 8, Jl. DR. Ide Anak Agung Gde Agung Kav. E.1.2 No. 1 & 2, Jakarta Selatan - DKI Jakarta - Indonesia</p>
                        <p><strong>Email:</strong><br> info@inheren.com</p>
                        <p><strong>Phone:</strong><br> +62 819 9869 6911</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card-clean p-0 overflow-hidden">
                        <iframe
                            src="https://www.google.com/maps?q=Plaza+Mutiara+Jakarta&output=embed"
                            style="width:100%; height:300px; border:0;">
                        </iframe>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <div class="footer-copyright">
        &copy; 2020 Inheren Certification. All rights reserved.
    </div>

</footer>
{{-- Fixed WhatsApp button, stays in place while scrolling --}}
<a href="https://wa.me/6281998696911?text=Halo%2C%20saya%20ingin%20menanyakan%20mengenai%20sertifikat%20ini"
   target="_blank"
   class="whatsapp-float"
   aria-label="Chat via WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>

</body>
</html>