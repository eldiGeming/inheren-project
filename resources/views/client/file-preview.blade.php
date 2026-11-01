{{-- File di modal detail. Ikon mata bersifat radio ($group per modal): satu file dipreview di panel kanan modal,
     tanpa tab baru. Jenis yang tidak bisa ditampilkan browser (mis. .doc lama, .zip) hanya bisa diunduh.
     $url = route file, $view = route client.view (Word/Excel). --}}
@php
    $mode = \App\Models\ClientAttachment::viewer($name, $mime);
    // Gambar tampil sebagai <img>; PDF/teks langsung dari file di iframe; Word/Excel lewat halaman viewer di iframe
    $kind = $mode === 'inline' && str_starts_with((string) $mime, 'image/') ? 'image' : 'frame';
    $src = $mode === 'inline' ? $url : $view.'?embed=1';
    // PDF di panel sempit: tanpa sidebar thumbnail, langsung pas lebar
    if ($mime === 'application/pdf') {
        $src .= '#view=FitH&navpanes=0';
    }
@endphp
<span class="file-item">
    @if($mode)
        <label class="file-item-open" title="Preview {{ $name }}">
            <input type="radio" class="file-preview-radio visually-hidden" name="{{ $group }}"
                data-src="{{ $src }}" data-kind="{{ $kind }}" data-name="{{ $name }}" data-download="{{ $url }}?download=1">
            <span class="file-item-eye" aria-hidden="true"><i class="bi bi-eye"></i></span>
            <span class="file-item-name">{{ $name }}</span>
        </label>
    @else
        <span class="file-item-open is-disabled" title="Format ini tidak bisa dipreview, unduh untuk membuka">
            <span class="file-item-eye" aria-hidden="true"><i class="bi bi-eye-slash"></i></span>
            <span class="file-item-name">{{ $name }}</span>
        </span>
    @endif
    <a href="{{ $url }}?download=1" class="file-item-download" title="Unduh {{ $name }}" aria-label="Unduh {{ $name }}">
        <i class="bi bi-download"></i>
    </a>
</span>
