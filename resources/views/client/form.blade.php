{{-- Isi form Data Client untuk modal Create ($row = null) dan Edit ($row = data client), tata letak sesuai mockup klien --}}
@php
    $suffix = $row ? '_'.$row->id_client : '';
    $uid = 'client'.($row ? $row->id_client : 'New');
    $group = $uid.'Preview'; // grup radio ikon mata file tersimpan (client.file-preview)
    $files = $row ? $row->attachments->keyBy('type') : collect();
    $legality = $files->get(\App\Models\ClientAttachment::LEGALITY);
    $selected = fn ($option, $value) => strcasecmp($option, $value ?? '') === 0 ? 'selected' : '';

    // Standard tersimpan yang tidak ada di daftar tetap tampil sebagai pilihan
    $standards = $isoOptions;
    if ($row && $row->iso_standard && ! $standards->contains(fn ($iso) => strcasecmp($iso, $row->iso_standard) === 0)) {
        $standards = $standards->concat([$row->iso_standard]);
    }
@endphp

<div class="modal-body">
    <div class="row gx-4">

        {{-- Kolom kiri: identitas client & kontak --}}
        <div class="col-lg-6 col-xl-4 client-col-main">
            <section class="client-panel">
                <h6 class="client-panel-title"><i class="bi bi-person-vcard" aria-hidden="true"></i>Client &amp; Kontak</h6>
                <div class="client-field">
                    <label class="form-label" for="{{ $uid }}_client_name">Client Name</label>
                    <div>
                        <select id="{{ $uid }}_client_name" name="client_name" class="form-control tag-select" data-placeholder="Select or type">
                            <option value=""></option>
                            @foreach($clientNames as $name)
                                <option value="{{ $name }}" {{ $selected($name, $row?->client_name) }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger small error-text" id="error_client_name{{ $suffix }}"></span>
                    </div>
                </div>

                <div class="client-field">
                    <label class="form-label" for="{{ $uid }}_scope">Scope</label>
                    <div>
                        <input type="text" id="{{ $uid }}_scope" name="scope" value="{{ $row?->scope }}" class="form-control">
                        <span class="text-danger small error-text" id="error_scope{{ $suffix }}"></span>
                    </div>
                </div>

                <div class="client-field">
                    <label class="form-label" for="{{ $uid }}_cb">CB</label>
                    <div>
                        <select id="{{ $uid }}_cb" name="cb" class="form-control tag-select" data-placeholder="Select or type">
                            <option value=""></option>
                            @foreach($cbOptions as $cb)
                                <option value="{{ $cb }}" {{ $selected($cb, $row?->cb) }}>{{ $cb }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger small error-text" id="error_cb{{ $suffix }}"></span>
                    </div>
                </div>

                <div class="client-field">
                    <label class="form-label" for="{{ $uid }}_pic_name">PIC Name</label>
                    <div>
                        <select id="{{ $uid }}_pic_name" name="pic_name" class="form-control tag-select" data-placeholder="Select or type" data-phone-field="pic_phone">
                            <option value=""></option>
                            @foreach($picOptions['names'] as $pic)
                                <option value="{{ $pic['name'] }}" data-hint="{{ $pic['phone'] }}" {{ $selected($pic['name'], $row?->pic_name) }}>{{ $pic['name'] }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger small error-text" id="error_pic_name{{ $suffix }}"></span>
                    </div>
                </div>

                <div class="client-field">
                    <label class="form-label" for="{{ $uid }}_pic_phone">PIC No. HP</label>
                    <div>
                        <select id="{{ $uid }}_pic_phone" name="pic_phone" class="form-control tag-select phone-select" data-placeholder="Select or type" data-name-field="pic_name">
                            <option value=""></option>
                            @foreach($picOptions['phones'] as $pic)
                                <option value="{{ $pic['phone'] }}" data-hint="{{ $pic['name'] }}" {{ $row && $pic['phone'] === $row->pic_phone ? 'selected' : '' }}>{{ $pic['phone'] }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger small error-text" id="error_pic_phone{{ $suffix }}"></span>
                    </div>
                </div>

                <div class="client-field">
                    <label class="form-label" for="{{ $uid }}_sales_name">Sales Name</label>
                    <div>
                        <select id="{{ $uid }}_sales_name" name="sales_name" class="form-control tag-select" data-placeholder="Select or type" data-phone-field="sales_phone">
                            <option value=""></option>
                            @foreach($salesOptions['names'] as $sales)
                                <option value="{{ $sales['name'] }}" data-hint="{{ $sales['phone'] }}" {{ $selected($sales['name'], $row?->sales_name) }}>{{ $sales['name'] }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger small error-text" id="error_sales_name{{ $suffix }}"></span>
                    </div>
                </div>

                <div class="client-field">
                    <label class="form-label" for="{{ $uid }}_sales_phone">Sales No. HP</label>
                    <div>
                        <select id="{{ $uid }}_sales_phone" name="sales_phone" class="form-control tag-select phone-select" data-placeholder="Select or type" data-name-field="sales_name">
                            <option value=""></option>
                            @foreach($salesOptions['phones'] as $sales)
                                <option value="{{ $sales['phone'] }}" data-hint="{{ $sales['name'] }}" {{ $row && $sales['phone'] === $row->sales_phone ? 'selected' : '' }}>{{ $sales['phone'] }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger small error-text" id="error_sales_phone{{ $suffix }}"></span>
                    </div>
                </div>
            </section>
        </div>

        {{-- Kolom tengah: legalitas, foto, dokumen, note --}}
        <div class="col-lg-6 col-xl-4 client-col-main">
            <section class="client-panel">
                <h6 class="client-panel-title"><i class="bi bi-folder2-open" aria-hidden="true"></i>Legalitas &amp; Dokumen</h6>
                <div class="client-field">
                    <label class="form-label" for="{{ $uid }}_legality">Legalitas</label>
                    <div>
                        <div class="client-file-input">
                            <input type="file" id="{{ $uid }}_legality" name="legality" class="form-control">
                            <i class="bi bi-folder2-open" aria-hidden="true"></i>
                        </div>
                        @if($legality)
                            @include('client.current-file', [
                                'url' => route('client.attachment', [$row->id_client, \App\Models\ClientAttachment::LEGALITY]),
                                'view' => route('client.view', [$row->id_client, \App\Models\ClientAttachment::LEGALITY]),
                                'name' => $legality->file_name, 'mime' => $legality->file_mime,
                            ])
                        @endif
                        <div class="client-hint">Semua jenis file, maks 5 MB</div>
                        <span class="text-danger small error-text" id="error_legality{{ $suffix }}"></span>
                    </div>
                </div>

                <div class="client-field">
                    <label class="form-label" for="{{ $uid }}_photo">Foto</label>
                    <div>
                        <div class="client-file">
                            @if($row?->photo_mime)
                                {{-- Thumbnail sekaligus ikon mata foto: klik untuk preview di panel modal --}}
                                <label class="file-current-open flex-shrink-0" title="Preview foto saat ini">
                                    <input type="radio" class="file-preview-radio visually-hidden" form="" name="{{ $group }}"
                                        data-src="{{ route('client.photo', $row->id_client) }}" data-kind="image" data-name="Foto {{ $row->client_name }}"
                                        data-download="{{ route('client.photo', $row->id_client) }}?download=1">
                                    <img src="{{ route('client.photo', $row->id_client) }}" class="file-current rounded border" loading="lazy" alt="Foto saat ini">
                                </label>
                            @endif
                            <div class="client-file-input">
                                <input type="file" id="{{ $uid }}_photo" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                                <i class="bi bi-camera" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="client-hint">JPG, PNG, WEBP, maks 2 MB</div>
                        <span class="text-danger small error-text" id="error_photo{{ $suffix }}"></span>
                    </div>
                </div>

                <div class="client-field">
                    <label class="form-label" for="{{ $uid }}_document">Dokumen</label>
                    <div>
                        <div class="client-file-input">
                            <input type="file" id="{{ $uid }}_document" name="document" class="form-control">
                            <i class="bi bi-folder2-open" aria-hidden="true"></i>
                        </div>
                        @if($row?->document_name)
                            @include('client.current-file', [
                                'url' => route('client.document', $row->id_client),
                                'view' => route('client.view', [$row->id_client, 'document']),
                                'name' => $row->document_name, 'mime' => $row->document_mime,
                            ])
                        @endif
                        <div class="client-hint">Semua jenis file, maks 5 MB</div>
                        <span class="text-danger small error-text" id="error_document{{ $suffix }}"></span>
                    </div>
                </div>

                <div class="client-field">
                    <label class="form-label" for="{{ $uid }}_note">Note</label>
                    <div>
                        <textarea id="{{ $uid }}_note" name="note" rows="4" class="form-control" placeholder="Catatan (opsional)">{{ $row?->note }}</textarea>
                        <span class="text-danger small error-text" id="error_note{{ $suffix }}"></span>
                    </div>
                </div>

                <div class="client-note">
                    <i class="bi bi-info-circle" aria-hidden="true"></i>
                    Lampiran Sertifikat &amp; Report: semua jenis file, maks 5 MB per file.
                    @if($row)
                        <br>File lama tetap tersimpan jika tidak memilih file baru.
                    @endif
                </div>
            </section>
        </div>

        {{-- Kolom kanan: standard & jadwal audit, tiap jadwal dengan Lampiran Sertifikat & Report --}}
        <div class="col-xl-4">
            <section class="client-panel">
                <h6 class="client-panel-title"><i class="bi bi-calendar2-check" aria-hidden="true"></i>Standard &amp; Jadwal Audit</h6>
                <div class="row gx-4">
                    <div class="col-lg-6 col-xl-12">
                        <div class="client-field">
                            <label class="form-label" for="{{ $uid }}_iso_standard">Standard</label>
                            <div>
                                <select id="{{ $uid }}_iso_standard" name="iso_standard" class="form-control tag-select" data-placeholder="Select or type">
                                    <option value=""></option>
                                    @foreach($standards as $iso)
                                        <option value="{{ $iso }}" {{ $selected($iso, $row?->iso_standard) }}>{{ $iso }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger small error-text" id="error_iso_standard{{ $suffix }}"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row gx-4">
                    @foreach(\App\Models\ClientAttachment::STAGES as $stage => $stageLabel)
                    <div class="col-lg-6 col-xl-12 client-stage">
                        <div class="client-stage-box">
                            <div class="client-field">
                                <label class="form-label" for="{{ $uid }}_{{ $stage }}_date">{{ $stageLabel }}</label>
                                <div>
                                    <input type="date" id="{{ $uid }}_{{ $stage }}_date" name="{{ $stage }}_date" value="{{ $row?->{$stage.'_date'} }}" class="form-control">
                                    <span class="text-danger small error-text" id="error_{{ $stage }}_date{{ $suffix }}"></span>
                                </div>
                            </div>

                            @foreach(\App\Models\ClientAttachment::KINDS as $kind => $kindLabel)
                            @php
                                $type = $stage.'_'.$kind;
                                $file = $files->get($type);
                            @endphp
                            <div class="client-field client-field-sub">
                                <label class="form-label" for="{{ $uid }}_{{ $type }}">{{ $kindLabel }}</label>
                                <div>
                                    <div class="client-file-input">
                                        <input type="file" id="{{ $uid }}_{{ $type }}" name="{{ $type }}" class="form-control" aria-label="{{ $stageLabel }} {{ $kindLabel }}">
                                        <i class="bi bi-folder2-open" aria-hidden="true"></i>
                                    </div>
                                    @if($file)
                                        @include('client.current-file', [
                                            'url' => route('client.attachment', [$row->id_client, $type]),
                                            'view' => route('client.view', [$row->id_client, $type]),
                                            'name' => $file->file_name, 'mime' => $file->file_mime,
                                        ])
                                    @endif
                                    <span class="text-danger small error-text" id="error_{{ $type }}{{ $suffix }}"></span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
        </div>

    </div>

    @if($row)
        @include('client.preview-panel', ['class' => 'client-preview-drawer'])
    @endif
</div>

<div class="modal-footer">
    <button type="reset" class="btn btn-client btn-client-clear"><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>Clear</button>
    <button type="submit" class="btn btn-client btn-client-save"><i class="bi bi-check2" aria-hidden="true"></i>{{ $row ? 'Update' : 'Save' }}</button>
</div>
