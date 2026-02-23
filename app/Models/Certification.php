<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    protected $table = 'certifications';

    protected $primaryKey = 'id_certification';

    protected $fillable = [
        'id_company',
        'id_type_certificate',
        'number_certificate',
        'issues_date',
        'expired_date',
        'barcode_certificate',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company', 'id_company');
    }

    public function type()
    {
        return $this->belongsTo(TypeCertificate::class, 'id_type_certificate', 'id_type_certificate');
    }
}
