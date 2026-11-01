{{-- Panel preview file, diisi JS dari ikon mata (.file-preview-radio) yang dipilih di modal yang sama.
     Modal detail: muncul di kanan dan modal melebar. Modal edit ($class = client-preview-drawer): menutupi kolom kiri
     & tengah form tanpa menggeser field, lampiran audit di kolom kanan tetap bisa dipilih. --}}
<aside class="client-preview {{ $class ?? '' }}" aria-label="Preview file" hidden>
    <div class="client-preview-head">
        <i class="bi bi-eye client-preview-icon" aria-hidden="true"></i>
        <span class="client-preview-name"></span>
        <a href="#" class="client-preview-action client-preview-download" title="Unduh file ini" aria-label="Unduh file ini">
            <i class="bi bi-download"></i>
        </a>
        <button type="button" class="client-preview-action client-preview-close" title="Tutup preview (Esc)" aria-label="Tutup preview">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="client-preview-stage"></div>
</aside>
