<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientAttachment extends Model
{
    // File Legalitas Data Client (satu per client), disimpan di tabel ini bersama lampiran jadwal audit
    public const LEGALITY = 'legality';

    // Jadwal audit Data Client, masing-masing punya Lampiran Sertifikat & Lampiran Report
    public const STAGES = [
        'ia' => 'IA',
        'sa1' => 'SA1',
        'sa2' => 'SA2',
        'rc' => 'RC',
    ];

    public const KINDS = [
        'certificate' => 'Lampiran Sertifikat',
        'report' => 'Lampiran Report',
    ];

    // Jenis file yang dibuka langsung di browser tanpa diunduh. Jenis lain selalu diunduh: Word/Excel tidak bisa
    // ditampilkan browser, sedangkan HTML/SVG bisa menjalankan script di dalam aplikasi.
    public const PREVIEWABLE = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'text/plain',
    ];

    protected $table = 'client_attachments';

    protected $primaryKey = 'id_client_attachment';

    protected $fillable = [
        'id_client',
        'type',
        'file',
        'file_name',
        'file_mime'
    ];

    // BLOB file tidak ikut ke JSON, diunduh lewat route client.attachment
    protected $hidden = [
        'file'
    ];

    public static function isPreviewable(?string $mime): bool
    {
        return in_array($mime, self::PREVIEWABLE, true);
    }

    // Nama field upload / nilai kolom type: Legalitas + lampiran jadwal audit, mis. ['ia_certificate' => 'IA Lampiran Sertifikat', ...]
    public static function types(): array
    {
        $types = [self::LEGALITY => 'Legalitas'];
        foreach (self::STAGES as $stage => $stageLabel) {
            foreach (self::KINDS as $kind => $kindLabel) {
                $types[$stage.'_'.$kind] = $stageLabel.' '.$kindLabel;
            }
        }

        return $types;
    }
}
