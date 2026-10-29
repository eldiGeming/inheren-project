<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientAttachment extends Model
{
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

    // Nama field upload / nilai kolom type, mis. ['ia_certificate' => 'IA Lampiran Sertifikat', ...]
    public static function types(): array
    {
        $types = [];
        foreach (self::STAGES as $stage => $stageLabel) {
            foreach (self::KINDS as $kind => $kindLabel) {
                $types[$stage.'_'.$kind] = $stageLabel.' '.$kindLabel;
            }
        }

        return $types;
    }
}
