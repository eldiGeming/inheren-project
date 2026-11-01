{{-- Nama file di modal detail: PDF/gambar/teks dibuka di tab baru tanpa diunduh, plus tombol unduh.
     Jenis lain (mis. Word, Excel) tidak bisa ditampilkan browser, jadi langsung diunduh. --}}
@if(\App\Models\ClientAttachment::isPreviewable($mime))
    <a href="{{ $url }}" target="_blank" rel="noopener" title="Buka {{ $name }} di tab baru">
        <i class="bi bi-eye me-1"></i>{{ $name }}
    </a>
    <a href="{{ $url }}?download=1" class="file-download ms-1" title="Unduh {{ $name }}" aria-label="Unduh {{ $name }}">
        <i class="bi bi-download"></i>
    </a>
@else
    <a href="{{ $url }}" title="Format ini tidak bisa dibuka di browser, file diunduh">
        <i class="bi bi-download me-1"></i>{{ $name }}
    </a>
    <span class="text-muted">(unduh untuk membuka)</span>
@endif
