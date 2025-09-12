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

    // Một student có thể được quét bởi nhiều groups
    public function groupStudents()
    {
        return $this->hasMany(GroupStudent::class);
    }

    // Lấy danh sách groups đã quét student này
    public function scannedByGroups()
    {
        return $this->belongsToMany(Group::class, 'group_student')
                    ->withPivot('scan_count', 'last_scanned_at')
                    ->withTimestamps();
    }
}
