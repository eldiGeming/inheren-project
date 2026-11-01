{{-- Tombol file lama di modal edit: PDF/gambar/teks dibuka di tab baru, jenis lain diunduh --}}
@php($preview = \App\Models\ClientAttachment::isPreviewable($mime))
<a href="{{ $url }}" @if($preview) target="_blank" rel="noopener" @endif class="file-current btn btn-light border flex-shrink-0 p-0"
    title="{{ $preview ? 'Lihat' : 'Unduh' }} {{ $label }} saat ini: {{ $name }}" aria-label="{{ $preview ? 'Lihat' : 'Unduh' }} {{ $label }} saat ini: {{ $name }}">
    <i class="bi {{ $preview ? 'bi-eye' : 'bi-download' }}"></i>
</a>
