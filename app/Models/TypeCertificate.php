<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TypeCertificate extends Model
{
    protected $table = 'type_certificates';
    protected $primaryKey = 'id_type_certificate';
        protected $fillable = [
        'type_certificate',
        'description_certificate',
        'scope_certificate',
        'logo_certificate',
        'is_deleted',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];
    protected $guarded = [];
}
