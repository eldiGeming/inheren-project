{{-- File yang sudah tersimpan, di bawah input modal edit (input file selalu bertulis "No file chosen").
     Nama file bisa dibuka tanpa diunduh, lihat client.file-link. --}}
<div class="client-current">
    <i class="bi bi-paperclip client-current-icon" aria-hidden="true" title="File saat ini"></i>
    <span class="visually-hidden">File saat ini:</span>
    <span class="client-current-file">@include('client.file-link')</span>
</div>
