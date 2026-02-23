<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeCertificate extends Model
{
    protected $table = 'type_certificates';
    protected $primaryKey = 'id_type_certificate'; // sesuaikan dengan DB
    protected $guarded = [];
}
