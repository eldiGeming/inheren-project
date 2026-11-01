{{-- File yang sudah tersimpan, di bawah input modal edit (input file selalu bertulis "No file chosen").
     Ikon mata menampilkan file di panel preview modal edit, lihat client.file-preview. --}}
<div class="client-current">
    <i class="bi bi-paperclip client-current-icon" aria-hidden="true" title="File saat ini"></i>
    <span class="visually-hidden">File saat ini:</span>
    @include('client.file-preview')
</div>
