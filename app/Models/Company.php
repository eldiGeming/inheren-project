<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'id_company'; // sesuaikan dengan DB
    protected $guarded = [];
}
