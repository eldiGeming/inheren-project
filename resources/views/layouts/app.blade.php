<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Inheren Sertifikasi</title>

    <script>
        // Terapkan dark mode tersimpan sebelum halaman tampil, supaya tidak berkedip terang
        try {
            if (localStorage.getItem('app-skin-dark') === 'app-skin-dark') {
                document.documentElement.classList.add('app-skin-dark');
            }
        } catch (e) {}
    </script>
    <link rel="icon" href="{{ asset('assets/images/logo-abbr.png') }}" type="image/png">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/dataTables.bs5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/select2-theme.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <style>
        body.modal-open .nxl-container {
            filter: none !important;
        }

        /* Kontrol bawaan browser (ikon kalender, tombol file, scrollbar) ikut gelap */
        html.app-skin-dark {
            color-scheme: dark;
        }

        html.app-skin-dark .form-control::file-selector-button,
        html.app-skin-dark .form-control:hover:not(:disabled):not([readonly])::file-selector-button {
            background-color: #1b2436;
            color: #b1b4c0;
        }

        /* Judul card di halaman tabel master (Company, Standard, Certification, Data Client): 30% lebih besar dari 16px bawaan */
        .card-title.page-title {
            font-size: 20.8px;
        }

        /* Theme membalik warna logo di dark mode (untuk logo bawaan template); logo Inheren sudah terbaca di latar gelap */
        html.app-skin-dark .nxl-navigation .m-header .logo-lg {
            filter: none;
        }
    </style>
</head>

<body>

@include('layouts.sidebar')
@include('layouts.header')

<main class="nxl-container">
    <div class="nxl-content">
        @yield('content')
    </div>

    @include('layouts.footer')
</main>

<!-- Vendors JS -->
<script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
<script src="{{ asset('assets/vendors/js/dataTables.min.js') }}"></script>
<script src="{{ asset('assets/vendors/js/dataTables.bs5.min.js') }}"></script>
<script src="{{ asset('assets/vendors/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/vendors/js/select2-active.min.js') }}"></script>
<script src="{{ asset('assets/js/common-init.min.js') }}"></script>

<script>
    // Tombol dark/light mode di header. Ikon diatur CSS theme lewat class app-skin-dark,
    // key localStorage sama dengan theme-customizer bawaan template.
    $(document).on('click', '.dark-button, .light-button', function (e) {
        e.preventDefault();
        let dark = $(this).hasClass('dark-button');
        $('html').toggleClass('app-skin-dark', dark);
        try {
            localStorage.setItem('app-skin-dark', dark ? 'app-skin-dark' : 'app-skin-light');
        } catch (err) {}
    });
</script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

@stack('scripts')

</body>
</html>