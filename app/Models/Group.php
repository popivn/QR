<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'festival_id',
        'name',
        'description'
    ];

    // Một group có nhiều users
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Một group có nhiều group_student records
    public function groupStudents()
    {
        return $this->hasMany(GroupStudent::class);
    }

    // Lấy danh sách students đã được quét trong group này
    public function scannedStudents()
    {
        return $this->belongsToMany(Student::class, 'group_student')
                    ->withPivot('scan_count', 'last_scanned_at')
                    ->withTimestamps();
    }

    // Quan hệ với Festival
    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }

    /**
     * Scope để lọc groups theo festival
     */
    public function scopeForFestival($query, $festivalId)
    {
        return $query->where('festival_id', $festivalId);
    }
}
