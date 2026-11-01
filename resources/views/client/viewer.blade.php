{{-- Viewer Word (.docx) & Excel (.xlsx/.xls/.csv/.ods) Data Client: file dibaca & dirender di browser, tanpa diunduh
     dan tanpa layanan luar. Tampilan meniru viewer PDF Chrome agar terasa sama dengan membuka PDF. --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $name }}</title>
    <link rel="icon" href="{{ asset('assets/images/logo-abbr.png') }}" type="image/png">
    <style>
        :root {
            color-scheme: dark;
        }

        body {
            margin: 0;
            background: #525659;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }

        .viewer-bar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 12px;
            height: 56px;
            padding: 0 16px;
            background: #323639;
            color: #f1f3f4;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.4);
        }

        .viewer-name {
            flex: 1;
            min-width: 0;
            overflow: hidden;
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .viewer-download {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 40px;
            padding: 0 16px;
            border-radius: 6px;
            background: #3454d1;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .viewer-download:hover,
        .viewer-download:focus-visible {
            background: #2a45b0;
        }

        .viewer-body {
            padding: 24px 16px 40px;
        }

        .viewer-status {
            padding: 48px 16px;
            color: #e8eaed;
            font-size: 15px;
            text-align: center;
        }

        /* Word: tiap halaman kertas putih dengan bayangan */
        .viewer-body .docx-wrapper {
            padding: 0;
            background: transparent;
        }

        .viewer-body .docx-wrapper > section.docx {
            margin-bottom: 24px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.45);
        }

        /* Excel: tab sheet + tabel */
        .sheet-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .sheet-tab {
            min-height: 40px;
            padding: 0 16px;
            border: 0;
            border-radius: 6px 6px 0 0;
            background: #3c4043;
            color: #e8eaed;
            font: inherit;
            font-size: 13px;
            cursor: pointer;
        }

        .sheet-tab[aria-selected="true"] {
            background: #fff;
            color: #202124;
            font-weight: 600;
        }

        .sheet-page {
            max-width: 1200px;
            margin: 0 auto;
            overflow: auto;
            border-radius: 0 6px 6px 6px;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.45);
            color-scheme: light;
        }

        .sheet-page table {
            border-collapse: collapse;
            font-size: 13px;
            color: #202124;
        }

        .sheet-page td {
            padding: 6px 10px;
            border: 1px solid #dadce0;
            white-space: nowrap;
            vertical-align: top;
        }

        /* Di dalam panel preview modal: tanpa bar judul, jarak lebih rapat */
        body.is-embed .viewer-body {
            padding: 12px;
        }

        body.is-embed .viewer-body .docx-wrapper > section.docx {
            margin-bottom: 12px;
        }
    </style>
</head>
<body class="{{ $embed ? 'is-embed' : '' }}">

@unless($embed)
<header class="viewer-bar">
    <div class="viewer-name" title="{{ $name }}">{{ $name }}</div>
    <a class="viewer-download" href="{{ $url }}?download=1">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/></svg>
        Unduh
    </a>
</header>
@endunless

<main class="viewer-body" id="viewer" data-src="{{ $url }}?download=1" data-kind="{{ $viewer }}">
    <div class="viewer-status" id="viewerStatus" role="status">Memuat dokumen...</div>
</main>

@if($viewer === 'docx')
    <script nonce="{{ $nonce }}" src="{{ asset('assets/vendors/js/jszip.min.js') }}"></script>
    <script nonce="{{ $nonce }}" src="{{ asset('assets/vendors/js/docx-preview.min.js') }}"></script>
@else
    <script nonce="{{ $nonce }}" src="{{ asset('assets/vendors/js/xlsx.full.min.js') }}"></script>
@endif
<script nonce="{{ $nonce }}">
(async function () {
    const main = document.getElementById('viewer');
    const status = document.getElementById('viewerStatus');

    // Excel: satu tab per sheet, tabel sheet aktif di bawahnya
    function renderWorkbook(workbook) {
        const tabs = document.createElement('div');
        tabs.className = 'sheet-tabs';
        tabs.setAttribute('role', 'tablist');
        const page = document.createElement('div');
        page.className = 'sheet-page';
        page.setAttribute('role', 'tabpanel');

        function show(name, tab) {
            tabs.querySelectorAll('.sheet-tab').forEach(function (other) {
                other.setAttribute('aria-selected', other === tab ? 'true' : 'false');
            });
            const sheet = workbook.Sheets[name];
            page.innerHTML = sheet['!ref']
                ? XLSX.utils.sheet_to_html(sheet, { header: '', footer: '' })
                : '<p style="padding:16px;margin:0;">Sheet kosong</p>';
        }

        workbook.SheetNames.forEach(function (name, index) {
            const tab = document.createElement('button');
            tab.type = 'button';
            tab.className = 'sheet-tab';
            tab.setAttribute('role', 'tab');
            tab.textContent = name;
            tab.addEventListener('click', function () { show(name, tab); });
            tabs.append(tab);
            if (index === 0) {
                show(name, tab);
            }
        });
        main.append(tabs, page);
    }

    try {
        const response = await fetch(main.dataset.src, { credentials: 'same-origin' });
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        const data = await response.arrayBuffer();

        if (main.dataset.kind === 'docx') {
            const page = document.createElement('div');
            main.append(page);
            await docx.renderAsync(data, page, null, {
                className: 'docx',
                inWrapper: true,
                breakPages: true,
                useBase64URL: false,
                renderHeaders: true,
                renderFooters: true,
            });

            // Halaman Word (lebar kertas tetap) diperkecil bila tidak muat, mis. di panel preview modal
            const wrapper = page.querySelector('.docx-wrapper');
            const fit = function () {
                wrapper.style.zoom = '';
                const available = main.clientWidth - 24;
                wrapper.style.zoom = wrapper.scrollWidth > available ? available / wrapper.scrollWidth : '';
            };
            fit();
            window.addEventListener('resize', fit);
        } else {
            renderWorkbook(XLSX.read(data, { type: 'array', cellDates: true }));
        }
        status.remove();
    } catch (error) {
        console.error(error);
        status.textContent = 'Dokumen tidak bisa ditampilkan (file rusak atau formatnya tidak didukung). Gunakan tombol Unduh untuk membukanya.';
    }
})();
</script>
</body>
</html>
