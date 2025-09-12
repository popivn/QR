<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'mssv',
        'name',
        'class',
        'qr_code_path'
    ];
}
