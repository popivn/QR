<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'festival_id',
        'mssv',
        'holot',
        'ten',
        'gioi',
        'ngay_sinh',
        'name',
        'class',
        'qr_code_path'
    ];

    protected $casts = [
        'ngay_sinh' => 'date',
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

    // Quan hệ với Festival
    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }

    /**
     * Scope để lọc students theo festival
     */
    public function scopeForFestival($query, $festivalId)
    {
        return $query->where('festival_id', $festivalId);
    }
}
