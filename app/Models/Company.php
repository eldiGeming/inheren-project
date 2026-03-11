<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'id_company'; // sesuaikan dengan DB
    protected $fillable = [
        'name_company',
        'address_company',
        'phone_company',
        'email_company',
        'description_company',
        'logo_company',
        'is_deleted',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];
    protected $guarded = [];
}
