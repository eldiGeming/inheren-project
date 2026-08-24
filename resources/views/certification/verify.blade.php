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
            opacity: 0.08;
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
        .status-icon-wrapper {
            position: relative;
            width: 90px;
            height: 90px;
            margin: 0 auto 1rem auto;
        }

        .status-icon-wrapper .main-icon {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.75rem;
        }

        .status-icon-wrapper.active .main-icon {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-icon-wrapper.expired .main-icon {
            background-color: #f8d7da;
            color: #842029;
        }

        /* Lock badge overlay, only shown when expired */
        .status-icon-wrapper .lock-overlay {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background-color: #842029;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            border: 3px solid #fff;
        }

        .status-badge {
            font-weight: bold;
            font-size: 1rem;
            padding: 0.5rem 0;
            border-radius: 8px;
            display: block;
            width: 100%;
            text-align: center;
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
                width: 76px;
                height: 76px;
            }

            .status-icon-wrapper .main-icon {
                width: 76px;
                height: 76px;
                font-size: 2.25rem;
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
            background-color: #ffffff;
            border-top: 1px solid #dee2e6;
        }

        .footer-logo {
            width: 64px;
            height: 64px;
            object-fit: contain;
            display: block;
            margin: 0 auto 1.25rem auto;
        }

        .footer-about {
            max-width: 680px;
            margin: 0 auto;
            text-align: center;
            color: #495057;
            line-height: 1.7;
        }

        .section {
            padding: 4rem 0;
        }

        .section-gray {
            background-color: #f8f9fa;
        }

        .section-title h2 {
            font-weight: 700;
            color: #212529;
        }

        .section-title p {
            color: #6c757d;
        }

        .card-clean {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 1.75rem;
            height: 100%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .card-clean p {
            margin-bottom: 1rem;
            color: #495057;
        }

        .card-clean p:last-child {
            margin-bottom: 0;
        }

        .footer-copyright {
            text-align: center;
            padding: 1.25rem 0;
            font-size: 0.875rem;
            color: #6c757d;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>

<div class="main-section">

    <img src="{{ asset('assets/images/logo-abbr.png') }}" alt="" class="bg-watermark">

    @php
        $isExpired = \Carbon\Carbon::parse($certification->expired_date)->isPast();
    @endphp

    <div class="content-wrapper">

        {{-- Status icon: verified badge, overlaid with lock icon when expired --}}
        <div class="status-icon-wrapper {{ $isExpired ? 'expired' : 'active' }}">
            <div class="main-icon">
                <i class="bi bi-patch-check-fill"></i>
            </div>
            @if($isExpired)
                <div class="lock-overlay">
                    <i class="bi bi-lock-fill"></i>
                </div>
            @endif
        </div>

        <div class="d-flex justify-content-center mb-4">
            @if($isExpired)
                <span class="status-badge status-expired">EXPIRED</span>
            @else
                <span class="status-badge status-active">ACTIVE</span>
            @endif
        </div>

        <hr>

        <div class="info-grid">
            <div class="info-label">Certificate Number</div>
            <div class="info-value">{{ $certification->number_certificate }}</div>

            <div class="info-label">Certificate Title</div>
            <div class="info-value">{{ $certification->type->type_certificate }}</div>
            
            <div class="info-label">Description</div>
            <div class="info-value">{{ $certification->type->description_certificate }}</div>

            <div class="info-label">Company</div>
            <div class="info-value">{{ $certification->company->name_company }}</div>

            <div class="info-label">Address</div>
            <div class="info-value">{{ $certification->company->address_company }}</div>


            <div class="info-label">Scope of Activities</div>
            <div class="info-value">{{ $certification->type->scope_certificate }}</div>

            <div class="info-label">Issue Date</div>
            <div class="info-value">{{ $certification->issues_date }}</div>

            <div class="info-label">Expired Date</div>
            <div class="info-value">{{ $certification->expired_date }}</div>
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