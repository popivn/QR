<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupStudent extends Model
{
    use HasFactory;

    protected $table = 'group_student';

    protected $fillable = [
        'group_id',
        'student_id',
        'scan_count',
        'last_scanned_at'
    ];

    protected $casts = [
        'last_scanned_at' => 'datetime',
    ];

    // Relationships
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Helper methods
    public function incrementScanCount()
    {
        $this->increment('scan_count');
        $this->update(['last_scanned_at' => now()]);
    }

    public static function recordScan($groupId, $studentId)
    {
        $groupStudent = self::firstOrCreate(
            [
                'group_id' => $groupId,
                'student_id' => $studentId
            ],
            [
                'scan_count' => 0,
                'last_scanned_at' => now()
            ]
        );

        $groupStudent->incrementScanCount();
        
        return $groupStudent;
    }
}