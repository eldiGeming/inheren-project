<?php

namespace App\Models;
use Carbon\Carbon;
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
        'reminder_sent_at',
        'is_deleted',
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

    
    protected $appends = [
        'issues_date_formatted',
        'expired_date_formatted'
    ];

    public function getIssuesDateFormattedAttribute()
    {
        return $this->issues_date 
            ? Carbon::parse($this->issues_date)->format('d-m-Y') 
            : null;
    }

    public function getExpiredDateFormattedAttribute()
    {
        return $this->expired_date 
            ? Carbon::parse($this->expired_date)->format('d-m-Y') 
            : null;
    }

}
