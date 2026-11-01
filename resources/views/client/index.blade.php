@extends('layouts.app')

@section('title', 'Data Client')

@section('content')
<style>
/* Hover baris di modal detail, mengikuti mode terang/gelap */
.detail-row{
    transition: background 0.3s;
}
.detail-row:hover{
    background: #f8f9fa;
}
html.app-skin-dark .detail-row:hover{
    background: #1b2436;
}

/* ===== Modal detail: file dengan ikon mata (radio) & panel preview di kanan ===== */
.detail-file{
    display: grid;
    gap: 4px;
    margin-top: 10px;
}
.detail-file-label{
    font-size: 12px;
    color: #667085;
}
.file-item{
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    font-size: 13px;
}
.file-item-open{
    display: flex;
    align-items: center;
    gap: 12px; /* jarak ikon mata ke nama file */
    min-width: 0;
    margin: 0;
    cursor: pointer;
}
.file-item-eye,
.file-item-download{
    flex-shrink: 0;
    display: inline-grid;
    place-items: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    font-size: 15px;
    transition: background-color 0.15s, border-color 0.15s, color 0.15s;
}
.file-item-eye{
    border: 1px solid #d0d5dd;
    background: #fff;
    color: #475467;
}
.file-item-name{
    min-width: 0;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.file-item-open:hover .file-item-eye{
    border-color: #3454d1;
    color: #3454d1;
}
.file-item-open:has(.file-preview-radio:checked) .file-item-eye{
    border-color: #3454d1;
    background: #3454d1;
    color: #fff;
}
.file-item-open:has(.file-preview-radio:checked) .file-item-name{
    color: #3454d1;
    font-weight: 600;
}
.file-item-open:has(.file-preview-radio:focus-visible) .file-item-eye{
    outline: 2px solid #3454d1;
    outline-offset: 2px;
}
.file-item-open.is-disabled{
    cursor: default;
}
.file-item-open.is-disabled .file-item-eye{
    border-style: dashed;
    background: transparent;
    color: #98a2b3;
}
.file-item-download{
    color: #475467;
}
.file-item-download:hover,
.file-item-download:focus-visible{
    background: #eef1fb;
    color: #3454d1;
}
html.app-skin-dark .detail-file-label{
    color: #98a2b3;
}
html.app-skin-dark .file-item-eye{
    border-color: #2a3650;
    background: #131c2e;
    color: #cbd5e1;
}
html.app-skin-dark .file-item-open:hover .file-item-eye{
    border-color: #6f8cf2;
    color: #a9bbff;
}
html.app-skin-dark .file-item-open:has(.file-preview-radio:checked) .file-item-eye{
    border-color: #3454d1;
    background: #3454d1;
    color: #fff;
}
html.app-skin-dark .file-item-open:has(.file-preview-radio:checked) .file-item-name{
    color: #a9bbff;
}
html.app-skin-dark .file-item-open.is-disabled .file-item-eye{
    background: transparent;
    color: #667085;
}
html.app-skin-dark .file-item-download:hover,
html.app-skin-dark .file-item-download:focus-visible{
    background: #1b2436;
}

/* Modal melebar ke kanan saat ada file yang dipreview; info & preview punya scroll sendiri */
.client-detail-dialog{
    transition: max-width 0.3s ease;
}
.client-preview{
    display: flex;
    flex-direction: column;
    min-height: 0;
    background: #f2f4f7;
}
@media (min-width: 992px){
    .client-detail-dialog.is-previewing{
        max-width: min(1440px, calc(100vw - 3rem));
    }
    .client-detail-dialog.is-previewing .client-detail-layout{
        display: grid;
        grid-template-columns: minmax(380px, 5fr) 7fr;
        height: min(78vh, 880px);
    }
    .client-detail-dialog.is-previewing .client-detail-info{
        overflow-y: auto;
    }
    .client-detail-dialog.is-previewing .client-preview{
        border-left: 1px solid #e4e7ec;
    }
}
@media (max-width: 991.98px){
    .client-preview{
        height: 70vh;
        border-top: 1px solid #e4e7ec;
    }
}
.client-preview-head{
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px 10px 16px;
    border-bottom: 1px solid #e4e7ec;
    background: #fff;
}
.client-preview-icon{
    color: #3454d1;
}
.client-preview-name{
    flex: 1;
    min-width: 0;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    font-size: 14px;
    font-weight: 600;
    color: #101828;
}
.client-preview-action{
    display: inline-grid;
    place-items: center;
    width: 36px;
    height: 36px;
    padding: 0;
    border: 1px solid #d0d5dd;
    border-radius: 8px;
    background: #fff;
    color: #344054;
}
.client-preview-action:hover,
.client-preview-action:focus-visible{
    border-color: #3454d1;
    color: #3454d1;
}
.client-preview-stage{
    position: relative;
    flex: 1;
    min-height: 0;
}
.client-preview-stage iframe{
    display: block;
    width: 100%;
    height: 100%;
    border: 0;
    background: #fff;
}
.client-preview-stage img{
    display: block;
    width: 100%;
    height: 100%;
    padding: 16px;
    object-fit: contain;
}
.client-preview-loading{
    position: absolute;
    inset: 0;
    display: grid;
    place-items: center;
    padding: 16px;
    text-align: center;
    font-size: 14px;
    color: #667085;
    background: inherit;
}
html.app-skin-dark .client-preview{
    background: #0b1220;
}
html.app-skin-dark .client-detail-dialog.is-previewing .client-preview,
html.app-skin-dark .client-preview,
html.app-skin-dark .client-preview-head{
    border-color: #1b2436;
}
html.app-skin-dark .client-preview-head{
    background: #0f172a;
}
html.app-skin-dark .client-preview-name{
    color: #e5e7eb;
}
html.app-skin-dark .client-preview-action{
    border-color: #2a3650;
    background: #131c2e;
    color: #e5e7eb;
}
html.app-skin-dark .client-preview-loading{
    color: #98a2b3;
}

/* Thumbnail foto & tombol unduh file lama di modal edit, setinggi input file */
.file-current{
    width: 40px;
    height: 40px;
    object-fit: cover;
    background-color: #f8f9fa;
    font-size: 0; /* sembunyikan teks alt selama gambar dimuat */
}
.file-current .bi{
    font-size: 15px;
}
html.app-skin-dark .file-current{
    background-color: #1b2436;
}

/* Tema memberi tiap field Select2 z-index 1, sehingga field di bawahnya (mis. Standard)
   menutupi dropdown yang sedang terbuka. Field yang terbuka dinaikkan di atas field lain. */
select + .select2-container--bootstrap-5.select2-container--open{
    z-index: 2;
}

/* ===== Form Data Client mengikuti mockup klien: label di kiri, field berbentuk pill bergaris ungu ===== */
.client-form{
    --client-line: #b9a3e0;        /* garis field saat diam */
    --client-line-strong: #8a4fd0; /* garis field saat hover & fokus */
    --client-accent: #6a2bb5;
    --client-ring: rgba(138, 79, 208, 0.18);
    --client-field: #ffffff;
    --client-surface: #f6f4fb;     /* latar body modal */
    --client-panel: #ffffff;
    --client-panel-line: #e9e3f5;
    --client-stage: #faf8fe;
    --client-text: #1d2433;
    --client-muted: #667085;
    --client-label: 8.25rem; /* muat "Lampiran Sertifikat" */
}
/* Layar lebar (3 kolom): kolom kiri & tengah cukup label pendek agar input lebih lebar */
@media (min-width: 1200px){
    .client-form .client-col-main{
        --client-label: 6.75rem;
    }
}
html.app-skin-dark .client-form{
    --client-line: #5a4d8a;
    --client-line-strong: #a77de0;
    --client-accent: #c4a3ef;
    --client-ring: rgba(167, 125, 224, 0.28);
    --client-field: #0d1424;
    --client-surface: #0b1120;
    --client-panel: #111a2e;
    --client-panel-line: #222b45;
    --client-stage: #0e1628;
    --client-text: #e5e7eb;
    --client-muted: #98a2b3;
}

/* Satu baris field: label di kiri, input (+ pesan error) di kanan */
.client-field{
    display: grid;
    grid-template-columns: var(--client-label) minmax(0, 1fr);
    column-gap: 0.75rem;
    align-items: start;
    margin-bottom: 0.625rem;
}
.client-field > .form-label{
    min-height: 40px;
    display: flex;
    align-items: center;
    margin: 0;
    line-height: 1.25;
}

/* Jadwal audit: IA/SA1/SA2/RC dengan dua lampiran di bawahnya */
.client-stage{
    margin-top: 0.375rem;
}
.client-field-sub > .form-label{
    padding-left: 0.875rem;
    font-weight: 500 !important;
    opacity: 0.85;
}

.client-form .form-control,
html.app-skin-dark .client-form .form-control,
.client-form .select2-container--bootstrap-5 .select2-selection{
    border-color: var(--client-line) !important;
    border-radius: 50rem;
    background-color: var(--client-field) !important;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.client-form .form-control:hover,
html.app-skin-dark .client-form .form-control:hover,
.client-form .form-control:focus,
html.app-skin-dark .client-form .form-control:focus,
.client-form .select2-container--bootstrap-5 .select2-selection:hover,
.client-form .select2-container--focus .select2-selection,
.client-form .select2-container--open .select2-selection{
    border-color: var(--client-line-strong) !important;
}
.client-form .form-control{
    height: 40px;
    padding: 0 16px;
    font-size: 13px;
    color: var(--client-text);
}
.client-form .client-field > .form-label{
    font-size: 12.5px;
    font-weight: 600;
    color: var(--client-text);
}
/* Note: kotak teks beberapa baris, sudut membulat mengikuti gaya pill */
.client-form textarea.form-control{
    height: auto;
    min-height: 96px;
    padding: 10px 16px;
    line-height: 1.5;
    border-radius: 14px;
    resize: vertical;
}
.client-form .form-control:focus,
.client-form .select2-container--focus .select2-selection,
.client-form .select2-container--open .select2-selection{
    box-shadow: 0 0 0 0.2rem var(--client-ring) !important;
}

/* Select2 setinggi input lain, panah segitiga ungu seperti mockup */
.client-form .select2-container--bootstrap-5 .select2-selection--single{
    min-height: 40px;
    padding: 0 2.25rem 0 11px;
    display: flex;
    align-items: center;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%236a2bb5' d='M3 5.5h10L8 11z'/%3e%3c/svg%3e");
    background-size: 14px 14px;
    background-position: right 14px center;
}
html.app-skin-dark .client-form .select2-container--bootstrap-5 .select2-selection--single{
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%23c4a3ef' d='M3 5.5h10L8 11z'/%3e%3c/svg%3e");
}
.client-form .select2-container--bootstrap-5.select2-container--open .select2-selection{
    border-radius: 50rem;
}
.client-form .select2-container--bootstrap-5 .select2-dropdown,
html.app-skin-dark .client-form .select2-container--bootstrap-5 .select2-dropdown{
    margin-top: 4px;
    border-color: var(--client-line);
    border-top: 1px solid var(--client-line);
    border-radius: 12px;
    overflow: hidden;
}

/* Input file: tombol bawaan browser disembunyikan, ikon kamera/folder di kanan seperti mockup */
.client-file{
    display: flex;
    gap: 0.5rem;
}
.client-file-input{
    position: relative;
    flex: 1;
    min-width: 0;
}
.client-file-input .form-control{
    padding-right: 2.75rem;
    line-height: 38px;
    cursor: pointer;
}
.client-file-input .form-control::file-selector-button{
    display: none;
}
.client-file-input > .bi{
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    font-size: 17px;
    line-height: 1;
    color: var(--client-accent);
    pointer-events: none;
}
/* Ikon kalender bawaan Chrome diganti ikon ungu yang sama gayanya dengan ikon folder */
.client-form input[type="date"]::-webkit-calendar-picker-indicator{
    width: 17px;
    height: 17px;
    margin-right: -1px;
    background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236a2bb5'%3e%3cpath d='M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857z'/%3e%3cpath d='M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2'/%3e%3c/svg%3e") center / contain no-repeat;
    cursor: pointer;
}
html.app-skin-dark .client-form input[type="date"]::-webkit-calendar-picker-indicator{
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23c4a3ef'%3e%3cpath d='M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857z'/%3e%3cpath d='M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2'/%3e%3c/svg%3e");
}

.client-hint{
    margin-top: 3px;
    padding-left: 16px;
    font-size: 11px;
    color: #6b7280;
}
/* File yang sudah tersimpan di bawah input modal edit: nama panjang dipotong (lengkap di tooltip),
   ikon unduh tetap sebaris */
.client-current{
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 4px;
    padding-left: 8px;
    font-size: 12px;
    line-height: 1.5;
}
.client-current-icon{
    flex-shrink: 0;
    color: #6b7280;
}
.client-current-file{
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
}
.client-current-file > a:first-child{
    min-width: 0;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.client-current-file > .file-download{
    flex-shrink: 0;
}
/* Keterangan "(unduh untuk membuka)" cukup di modal detail; di sini tooltip & ikon unduh sudah menjelaskan */
.client-current-file > .text-muted{
    display: none;
}
html.app-skin-dark .client-current-icon{
    color: #9ca3af;
}
.client-note{
    margin-top: 0.75rem;
    padding: 0.625rem 0.875rem;
    border-left: 3px solid var(--client-line);
    border-radius: 4px;
    background-color: rgba(138, 79, 208, 0.06);
    font-size: 12px;
    line-height: 1.6;
    color: #4b5563;
}
html.app-skin-dark .client-hint,
html.app-skin-dark .client-note{
    color: #9ca3af;
}
html.app-skin-dark .client-note{
    background-color: rgba(167, 125, 224, 0.08);
}

/* ===== Tampilan modern modal Create/Edit (posisi field tetap seperti mockup) ===== */
.client-form .modal-body{
    padding: 20px;
    background: var(--client-surface);
}
/* Tiap kolom menjadi panel berjudul, tinggi panel dalam satu baris disamakan */
.client-form .modal-body > .row{
    row-gap: 16px;
}
.client-panel{
    padding: 16px 16px 8px;
    border: 1px solid var(--client-panel-line);
    border-radius: 16px;
    background: var(--client-panel);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
}
.client-panel-title{
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0 0 14px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--client-panel-line);
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.01em;
    color: var(--client-text);
}
.client-panel-title .bi{
    display: inline-grid;
    place-items: center;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(138, 79, 208, 0.12);
    color: var(--client-accent);
    font-size: 14px;
}

/* Tahap audit (IA/SA1/SA2/RC) dalam blok sendiri; label tahap berwarna, lampiran bergaris penghubung */
.client-stage-box{
    --client-label: 6.5rem;
    padding: 10px 12px 2px;
    border: 1px solid var(--client-panel-line);
    border-radius: 14px;
    background: var(--client-stage);
}
.client-stage-box > .client-field:first-child > .form-label{
    color: var(--client-accent) !important;
    font-weight: 700;
    letter-spacing: 0.02em;
}
.client-field-sub > .form-label::before{
    content: "";
    flex-shrink: 0;
    width: 8px;
    height: 1px;
    margin-right: 6px;
    background: var(--client-line);
}

/* Input file bergaris putus-putus agar beda dari isian teks */
.client-file-input .form-control,
html.app-skin-dark .client-file-input .form-control{
    border-style: dashed !important;
}
.client-file-input .form-control:hover{
    background-color: rgba(138, 79, 208, 0.04) !important;
}

/* File yang tersimpan tampil sebagai chip */
.client-current-file{
    padding: 2px 10px;
    border-radius: 50rem;
    background: rgba(138, 79, 208, 0.08);
}
html.app-skin-dark .client-current-file{
    background: rgba(167, 125, 224, 0.14);
}

/* Catatan info dengan ikon */
.client-note{
    display: flex;
    gap: 8px;
    border-left: 0;
    border-radius: 12px;
}
.client-note > .bi{
    flex-shrink: 0;
    color: var(--client-accent);
    font-size: 15px;
    line-height: 1.3;
}

@media (min-width: 1200px){
    .client-form-dialog{
        max-width: min(1320px, calc(100vw - 2rem));
    }
}

/* Header: bersih dengan ikon & garis aksen (biru = tambah, kuning = edit) */
.client-form-header{
    position: relative;
    overflow: hidden; /* garis aksen ikut sudut membulat modal (rounded-4) */
    border-radius: 1rem 1rem 0 0;
    padding: 18px 20px 16px;
    border-bottom: 1px solid var(--client-panel-line);
    background: var(--client-panel);
}
.client-form-header::before{
    content: "";
    position: absolute;
    inset: 0 0 auto;
    height: 4px;
    border-radius: inherit;
}
.client-form-header.is-create::before{
    background: #3454d1;
}
.client-form-header.is-edit::before{
    background: #f59e0b;
}
.client-form-heading{
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}
.client-form-badge{
    flex-shrink: 0;
    display: inline-grid;
    place-items: center;
    width: 40px;
    height: 40px;
    border-radius: 12px;
    font-size: 18px;
}
.is-create .client-form-badge{
    background: rgba(52, 84, 209, 0.12);
    color: #3454d1;
}
.is-edit .client-form-badge{
    background: rgba(245, 158, 11, 0.16);
    color: #b45309;
}
.client-form-header .modal-title{
    font-size: 16px;
    font-weight: 700;
    color: var(--client-text);
}
.client-form-subtitle{
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    font-size: 12.5px;
    color: var(--client-muted);
}
html.app-skin-dark .is-create .client-form-badge{
    background: rgba(99, 130, 245, 0.18);
    color: #a9bbff;
}
html.app-skin-dark .is-edit .client-form-badge{
    background: rgba(245, 158, 11, 0.16);
    color: #fbbf24;
}
html.app-skin-dark .client-form-header .btn-close{
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* Footer menempel di bawah layar selama form di-scroll */
.client-form .modal-footer{
    position: sticky;
    bottom: 0;
    z-index: 3;
    gap: 10px;
    padding: 14px 20px;
    border-top: 1px solid var(--client-panel-line);
    background: var(--client-panel);
    box-shadow: 0 -6px 16px rgba(16, 24, 40, 0.06);
}

/* Tombol CLEAR (merah muda) & SAVE (hijau) seperti mockup, versi lebih tegas dengan ikon */
.client-form .btn-client{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-width: 128px;
    min-height: 44px;
    padding: 0 24px;
    border: 1px solid transparent;
    border-radius: 50rem;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.02em;
    transition: background-color 0.15s, box-shadow 0.15s, transform 0.15s;
}
.client-form .btn-client .bi{
    font-size: 15px;
}
.client-form .btn-client:active{
    transform: translateY(1px);
}
.client-form .btn-client-clear{
    border-color: #fecdd3;
    background-color: #fff1f2;
}
.client-form .btn-client-clear:hover,
.client-form .btn-client-clear:focus-visible{
    background-color: #ffe4e6;
}
.client-form .btn-client-save{
    background-color: #16a34a;
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);
}
.client-form .btn-client-save:hover,
.client-form .btn-client-save:focus-visible{
    background-color: #15803d;
}
html.app-skin-dark .client-form .btn-client-clear{
    border-color: rgba(244, 63, 94, 0.35);
    background-color: rgba(244, 63, 94, 0.12);
}
html.app-skin-dark .client-form .btn-client-clear:hover,
html.app-skin-dark .client-form .btn-client-clear:focus-visible{
    background-color: rgba(244, 63, 94, 0.2);
}
/* Tema memaksa warna teks tombol (putih saat fokus/klik & di dark mode); tetapkan warna sesuai latar tombol */
.client-form .btn-client-clear,
.client-form .btn-client-clear:hover,
.client-form .btn-client-clear:focus,
.client-form .btn-client-clear:active{
    color: #be123c !important;
}
html.app-skin-dark .client-form .btn-client-clear,
html.app-skin-dark .client-form .btn-client-clear:hover,
html.app-skin-dark .client-form .btn-client-clear:focus,
html.app-skin-dark .client-form .btn-client-clear:active{
    color: #fda4af !important;
}
.client-form .btn-client-save,
.client-form .btn-client-save:hover,
.client-form .btn-client-save:focus,
.client-form .btn-client-save:active,
html.app-skin-dark .client-form .btn-client-save,
html.app-skin-dark .client-form .btn-client-save:hover,
html.app-skin-dark .client-form .btn-client-save:focus,
html.app-skin-dark .client-form .btn-client-save:active{
    color: #fff !important;
}
</style>

@php
    // Format tanggal untuk tampilan tabel & detail
    $dateText = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d-F-Y') : null;
@endphp

<div class="main-content">
    <div class="card stretch stretch-full">
        <div class="d-flex justify-content-between align-items-center p-3">
            <h5 class="card-title page-title mb-0">Data Client</h5>
            <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#createClientModal">
                <i class="feather-plus me-1"></i> Create
            </button>
        </div>

        <x-list-toolbar table="clientList" :export="route('client.export')"
            :dates="[6 => 'IA']"
            :sorts="['6:desc' => 'IA terbaru', '6:asc' => 'IA terlama']" />

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-list table-hover table-borderless align-middle mb-0"
                       id="clientList"
                       style="min-width: 900px;">

                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Client Name</th>
                            <th>Standard</th>
                            <th>CB</th>
                            <th>Sales Name</th>
                            <th>PIC Name</th>
                            <th>IA</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($allClient as $key => $row)
                        <tr id="clientRow{{ $row->id_client }}" data-id="{{ $row->id_client }}">

                            <td class="text-center">{{ $key + 1 }}</td>

                            <td>{{ $row->client_name }}</td>

                            <td>{{ $row->iso_standard ?: '-' }}</td>

                            <td>{{ $row->cb ?: '-' }}</td>

                            <td>
                                {{ $row->sales_name ?: '-' }}
                                @if($row->sales_phone)
                                    <div class="small text-muted">{{ $row->sales_phone }}</div>
                                @endif
                            </td>

                            <td>
                                {{ $row->pic_name ?: '-' }}
                                @if($row->pic_phone)
                                    <div class="small text-muted">{{ $row->pic_phone }}</div>
                                @endif
                            </td>

                            <td class="text-center" data-order="{{ $row->ia_date }}">{{ $dateText($row->ia_date) ?: '-' }}</td>

                            <td class="note-cell">
                                @if($row->note)
                                    <span title="{{ $row->note }}">{{ $row->note }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-1 flex-nowrap">
                                    <x-action-dropdown>
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#clientModal{{ $row->id_client }}">
                                                <i class="feather-eye me-2 text-info"></i>Lihat
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#updateClientModal{{ $row->id_client }}">
                                                <i class="feather-edit me-2 text-warning"></i>Edit
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button type="button" class="dropdown-item text-danger deleteClientBtn"
                                                data-url="{{ route('client.destroy', $row->id_client) }}"
                                                data-id="{{ $row->id_client }}">
                                                <i class="feather-trash-2 me-2"></i>Hapus
                                            </button>
                                        </li>
                                    </x-action-dropdown>
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
<div class="modal fade" id="createClientModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered client-form-dialog">
        <div class="modal-content rounded-4">
            <form id="createClientForm" class="client-form" action="{{ route('client.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header client-form-header is-create">
                    <div class="client-form-heading">
                        <span class="client-form-badge" aria-hidden="true"><i class="bi bi-person-plus"></i></span>
                        <div>
                            <h5 class="modal-title">Data Client</h5>
                            <div class="client-form-subtitle">Tambah client baru</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                @include('client.form', ['row' => null])
            </form>
        </div>
    </div>
</div>

{{-- ================= MODAL UPDATE ================= --}}
@foreach($allClient as $row)
<div class="modal fade" id="updateClientModal{{ $row->id_client }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered client-form-dialog">
        <div class="modal-content rounded-4">
            <form class="updateClientForm client-form" data-id="{{ $row->id_client }}" action="{{ route('client.update', $row->id_client) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="modal-header client-form-header is-edit">
                    <div class="client-form-heading">
                        <span class="client-form-badge" aria-hidden="true"><i class="bi bi-pencil-square"></i></span>
                        <div>
                            <h5 class="modal-title">Edit Data Client</h5>
                            <div class="client-form-subtitle">{{ $row->client_name }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                @include('client.form', ['row' => $row])
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- ================= MODAL DETAIL ================= --}}
@foreach($allClient as $row)
<div class="modal fade client-detail-modal" id="clientModal{{ $row->id_client }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered client-detail-dialog">
        <div class="modal-content rounded-4 shadow-sm">
            <!-- Header -->
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Client Detail</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body: info client di kiri; panel preview file muncul di kanan saat ikon mata dipilih --}}
            @php($group = 'preview'.$row->id_client)
            <div class="client-detail-layout">
            <div class="modal-body client-detail-info">
                @foreach([
                    'Client Name' => $row->client_name,
                    'Scope' => $row->scope,
                    'CB' => $row->cb,
                    'PIC Name' => $row->pic_name,
                    'No. HP PIC' => $row->pic_phone,
                    'Sales Name' => $row->sales_name,
                    'No. HP Sales' => $row->sales_phone,
                    'Standard' => $row->iso_standard,
                ] as $label => $value)
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">{{ $label }}</div>
                    <div class="col-md-8">{{ $value ?: '-' }}</div>
                </div>
                @endforeach

                @php($files = $row->attachments->keyBy('type'))
                @php($legality = $files->get(\App\Models\ClientAttachment::LEGALITY))
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Legalitas</div>
                    <div class="col-md-8">
                        @if($legality)
                            @include('client.file-preview', [
                                'url' => route('client.attachment', [$row->id_client, \App\Models\ClientAttachment::LEGALITY]),
                                'view' => route('client.view', [$row->id_client, \App\Models\ClientAttachment::LEGALITY]),
                                'name' => $legality->file_name, 'mime' => $legality->file_mime,
                            ])
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Note</div>
                    <div class="col-md-8" style="white-space: pre-line;">{{ $row->note ?: '-' }}</div>
                </div>

                {{-- Jadwal audit beserta Lampiran Sertifikat & Report yang bisa dibuka / diunduh --}}
                @foreach(\App\Models\ClientAttachment::STAGES as $stage => $stageLabel)
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">{{ $stageLabel }}</div>
                    <div class="col-md-8">
                        {{ $dateText($row->{$stage.'_date'}) ?: '-' }}
                        @foreach(\App\Models\ClientAttachment::KINDS as $kind => $kindLabel)
                            @php($file = $files->get($stage.'_'.$kind))
                            <div class="detail-file">
                                <span class="detail-file-label">{{ $kindLabel }}</span>
                                @if($file)
                                    @include('client.file-preview', [
                                        'url' => route('client.attachment', [$row->id_client, $stage.'_'.$kind]),
                                        'view' => route('client.view', [$row->id_client, $stage.'_'.$kind]),
                                        'name' => $file->file_name, 'mime' => $file->file_mime,
                                    ])
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Foto</div>
                    <div class="col-md-8">
                        @if($row->photo_mime)
                            <img src="{{ route('client.photo', $row->id_client) }}" width="150" class="img-thumbnail d-block mb-2" loading="lazy" alt="Foto {{ $row->client_name }}">
                            @include('client.file-preview', [
                                'url' => route('client.photo', $row->id_client), 'view' => null,
                                'name' => 'Foto '.$row->client_name, 'mime' => $row->photo_mime,
                            ])
                        @else
                            <span class="text-muted">No Foto</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3 py-2 border-bottom detail-row">
                    <div class="col-md-4 fw-bold">Dokumen</div>
                    <div class="col-md-8">
                        @if($row->document_name)
                            @include('client.file-preview', [
                                'url' => route('client.document', $row->id_client),
                                'view' => route('client.view', [$row->id_client, 'document']),
                                'name' => $row->document_name, 'mime' => $row->document_mime,
                            ])
                        @else
                            <span class="text-muted">No Dokumen</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Panel preview: diisi JS dari ikon mata yang dipilih --}}
            <aside class="client-preview" aria-label="Preview file" hidden>
                <div class="client-preview-head">
                    <i class="bi bi-eye client-preview-icon" aria-hidden="true"></i>
                    <span class="client-preview-name"></span>
                    <a href="#" class="client-preview-action client-preview-download" title="Unduh file ini" aria-label="Unduh file ini">
                        <i class="bi bi-download"></i>
                    </a>
                    <button type="button" class="client-preview-action client-preview-close" title="Tutup preview" aria-label="Tutup preview">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="client-preview-stage"></div>
            </aside>
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

    // Default urut tanggal IA terbaru; kolom No & Action tidak bisa diurutkan
    let table = $('#clientList').DataTable({
        pageLength: 10,
        order: [[6, 'desc']],
        columnDefs: [{ targets: [0, -1], orderable: false, searchable: false }]
    });
    setupListToolbar(table);

    // Dropdown selalu menempel tepat di bawah field. Bawaan Select2 (attachBody) menghitung
    // posisi dengan JS sehingga di dalam modal yang di-scroll bisa meleset atau berbalik ke atas.
    let s2 = $.fn.select2.amd.require;
    let Utils = s2('select2/utils');
    function AttachBelowField(decorated, $element, options){
        decorated.call(this, $element, options);
    }
    AttachBelowField.prototype.position = function(decorated, $dropdown, $container){
        $container.find('.dropdown-wrapper').removeAttr('aria-hidden').append($dropdown);
        $dropdown.addClass('select2-dropdown--below');
        $container.addClass('select2-container--below');
    };
    let tagDropdown = Utils.Decorate(
        Utils.Decorate(Utils.Decorate(s2('select2/dropdown'), s2('select2/dropdown/search')), s2('select2/dropdown/closeOnSelect')),
        AttachBelowField
    );

    // Nomor HP: angka dengan pemisah spasi / - / . / kurung, boleh diawali +, minimal 6 digit (sama dengan validasi server)
    const PHONE_PATTERN = /^\+?(?:[\s().-]*\d){6,}[\s().-]*$/;

    // Keterangan di samping pilihan: nomor HP terakhir untuk nama, nama pemilik untuk nomor HP
    function hintOf(item){
        return item.element ? ($(item.element).attr('data-hint') || '') : '';
    }

    // Pilih value tanpa membedakan huruf besar/kecil; value baru ditambahkan sebagai pilihan
    function setTagValue(select, value){
        let option = select.find('option').filter(function(){
            return this.value !== '' && this.value.toLowerCase() === value.toLowerCase();
        });
        if (!option.length) {
            option = $(new Option(value, value));
            select.append(option);
        }
        select.val(option.val()).trigger('change');
        return option[0];
    }

    // Client Name, CB, Sales, PIC, No. HP & Standard: pilih dari daftar atau ketik entri baru.
    // Entri baru ikut tersimpan bersama data client, jadi muncul di daftar pada input berikutnya.
    $('.tag-select').each(function(){
        let select = $(this);
        let isPhone = select.hasClass('phone-select');
        let picked = false;

        select.select2({
            theme: 'bootstrap-5',
            tags: true,
            width: '100%',
            dropdownAdapter: tagDropdown,
            // Cari juga lewat keterangan, mis. ketik nama Sales untuk menemukan nomornya
            matcher: function(params, item){
                let term = $.trim(params.term || '').toLowerCase();
                if (!term || (item.text + ' ' + hintOf(item)).toLowerCase().includes(term)) {
                    return item;
                }
                return null;
            },
            templateResult: function(item){
                let hint = hintOf(item);
                if (!hint) {
                    return item.text;
                }
                return $('<span>').append(
                    $('<span class="d-block">').text(item.text),
                    $('<small class="d-block opacity-75">').text(hint)
                );
            },
            createTag: function(params){
                let term = $.trim(params.term);
                if (!term || (isPhone && !PHONE_PATTERN.test(term))) {
                    return null;
                }
                return { id: term, text: term };
            },
            // Entri baru di urutan terakhir, sehingga data yang sudah ada yang tersorot lebih dulu
            insertTag: function(data, tag){
                data.push(tag);
            }
        }).on('select2:open', function(){
            picked = false;
            // Pastikan daftar pilihan terlihat penuh di dalam modal (setelah hasil selesai di-render)
            let dropdown = select.next('.select2-container').find('.select2-dropdown')[0];
            setTimeout(function(){
                dropdown.scrollIntoView({ block: 'nearest' });
            });
        }).on('select2:selecting', function(){
            picked = true;
        }).on('select2:closing', function(e){
            let original = e.params.args && e.params.args.originalEvent;
            if (original && (original.key === 'Escape' || original.which === 27)) {
                // Esc hanya menutup dropdown & membatalkan ketikan, modal jangan ikut tertutup
                original.stopPropagation();
                return;
            }
            // Sudah mengetik tapi field ditinggalkan (klik di luar) tanpa memilih: pakai pilihan yang tersorot,
            // sama seperti menekan Enter, agar ketikan tidak hilang
            let container = select.next('.select2-container');
            let term = $.trim(container.find('.select2-search__field').val());
            let highlighted = container.find('.select2-results__option--highlighted')[0];
            let data = highlighted && Utils.GetData(highlighted, 'data');
            if (picked || !term || !data || data.id === undefined) {
                return;
            }
            let option = setTagValue(select, String(data.id));
            select.trigger({ type: 'select2:select', params: { data: { id: option.value, element: option } } });
        });
    });

    // Nama Sales/PIC dipilih: isi nomor HP terakhir orang itu. Jika orang itu belum punya nomor,
    // kosongkan nomor yang tercatat milik orang lain (nomor baru yang belum ada pemiliknya dibiarkan).
    $('.tag-select[data-phone-field]').on('select2:select', function(e){
        let phone = $(this).closest('form').find('select[name="' + $(this).data('phone-field') + '"]');
        let known = hintOf(e.params.data);
        let owner = phone.find('option:selected').attr('data-hint');
        if (known) {
            setTagValue(phone, known);
        } else if (owner && owner.toLowerCase() !== String(e.params.data.id).toLowerCase()) {
            phone.val(null).trigger('change');
        }
    });

    // Nomor HP dipilih saat nama masih kosong: isi nama pemilik nomor itu
    $('.tag-select[data-name-field]').on('select2:select', function(e){
        let name = $(this).closest('form').find('select[name="' + $(this).data('name-field') + '"]');
        let owner = hintOf(e.params.data);
        if (owner && !name.val()) {
            setTagValue(name, owner);
        }
    });

    // ================= PREVIEW FILE DI MODAL DETAIL =================
    // Ikon mata bersifat radio: file yang dipilih tampil di panel kanan dan modal melebar, tanpa membuka tab baru
    function closePreview(modal){
        modal.find('.file-preview-radio').prop('checked', false);
        modal.find('.client-preview-stage').empty();
        modal.find('.client-preview').prop('hidden', true);
        modal.find('.client-detail-dialog').removeClass('is-previewing');
    }

    $(document).on('change', '.file-preview-radio', function(){
        let modal = $(this).closest('.modal');
        let panel = modal.find('.client-preview');
        let file = this.dataset;
        let loading = $('<div class="client-preview-loading" role="status">').text('Memuat preview...');
        let media = file.kind === 'image'
            ? $('<img>').attr('alt', 'Preview ' + file.name)
            : $('<iframe>').attr('title', 'Preview ' + file.name);

        media.on('load', function(){
            loading.remove();
        }).on('error', function(){
            loading.text('Preview gagal dimuat. Gunakan tombol unduh di atas.');
        });

        panel.find('.client-preview-name').text(file.name).attr('title', file.name);
        panel.find('.client-preview-download').attr('href', file.download);
        panel.find('.client-preview-stage').empty().append(media, loading);
        media.attr('src', file.src);
        panel.prop('hidden', false);
        modal.find('.client-detail-dialog').addClass('is-previewing');
    });

    $(document).on('click', '.client-preview-close', function(){
        closePreview($(this).closest('.modal'));
    });

    // Modal ditutup: preview & pilihan ikon mata direset
    $('.client-detail-modal').on('hidden.bs.modal', function(){
        closePreview($(this));
    });

    // Tombol Clear: hapus pesan error dan sinkronkan tampilan Select2 setelah form di-reset
    $('#createClientForm, .updateClientForm').on('reset', function(){
        let form = $(this);
        form.find('.error-text').text('');
        setTimeout(function(){
            form.find('.tag-select').trigger('change');
        });
    });

    // Gagal simpan: 422 tampilkan pesan di bawah field (dan gulir ke error pertama, karena form panjang),
    // 413 total upload melebihi batas ukuran request server
    function showSaveError(form, err, suffix){
        if (err.status === 422) {
            $.each(err.responseJSON.errors, function(key, val){
                $('#error_' + key + suffix).text(val[0]);
            });
            let first = form.find('.error-text').filter(function(){ return this.textContent; })[0];
            if (first) {
                first.closest('.client-field').scrollIntoView({ block: 'center' });
            }
        } else if (err.status === 413) {
            Swal.fire({
                icon: 'error',
                title: 'File terlalu besar',
                text: 'Total ukuran file yang di-upload melebihi batas server. Simpan sebagian file dulu, lalu tambahkan sisanya lewat Edit.'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Server error occurred.'
            });
        }
    }

    // ================= CREATE =================
    $('#createClientForm').submit(function(e){
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

                $('#createClientModal').modal('hide');
                form[0].reset();

                // Reload agar modal detail/edit (foto, dokumen) untuk data baru ikut ter-render
                Swal.fire({
                    icon: 'success',
                    title: 'Created!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(function(){
                    location.reload();
                });
            },
            error: function(err){
                showSaveError(form, err, '');
            }
        });
    });


    // ================= UPDATE =================
    $('.updateClientForm').submit(function(e){
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

                $('#updateClientModal'+id).modal('hide');

                // Reload agar modal detail (foto, dokumen, jadwal audit) ikut ter-update
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(function(){
                    location.reload();
                });
            },
            error: function(err){
                showSaveError(form, err, '_' + id);
            }
        });
    });


    // ================= DELETE =================
    $(document).on('click', '.deleteClientBtn', function(){

        let url = $(this).data('url');
        let id = $(this).data('id');
        let row = $('#clientRow' + id);

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

                        // Nomor urut diperbarui otomatis saat tabel di-draw ulang
                        table.row(row).remove().draw(false);

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
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
