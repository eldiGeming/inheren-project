<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $primaryKey = 'id_client';

    protected $fillable = [
        'client_name',
        'sales_name',
        'sales_phone',
        'scope',
        'legality',
        'pic_name',
        'pic_phone',
        'iso_standard',
        'ia_date',
        'sa1_date',
        'sa2_date',
        'rc_date',
        'is_deleted',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];

    // BLOB foto & dokumen tidak ikut ke JSON, diakses lewat route client.photo / client.document
    protected $hidden = [
        'photo',
        'document'
    ];
}
