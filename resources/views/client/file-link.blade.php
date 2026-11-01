{{-- Nama file di modal detail: dibuka di tab baru tanpa diunduh (PDF, gambar, teks, Word .docx, Excel), plus tombol unduh.
     Jenis lain (mis. .doc lama, .pptx, .zip) tidak bisa ditampilkan browser, jadi langsung diunduh.
     $url = route file, $view = route client.view file itu. --}}
@if(\App\Models\ClientAttachment::viewer($name, $mime))
    <a href="{{ $view }}" target="_blank" rel="noopener" title="Buka {{ $name }} di tab baru">
        <i class="bi bi-eye me-1"></i>{{ $name }}
    </a>
    <a href="{{ $url }}?download=1" class="file-download ms-1" title="Unduh {{ $name }}" aria-label="Unduh {{ $name }}">
        <i class="bi bi-download"></i>
    </a>
@else
    <a href="{{ $url }}?download=1" title="Format ini tidak bisa dibuka di browser, file diunduh">
        <i class="bi bi-download me-1"></i>{{ $name }}
    </a>
    <span class="text-muted">(unduh untuk membuka)</span>
@endif
