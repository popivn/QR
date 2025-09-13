<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Festival extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Quan hệ với User (người tạo lễ hội)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Quan hệ với Groups trong lễ hội
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Quan hệ với Students trong lễ hội
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Quan hệ với AuditLogs trong lễ hội
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Scope để lọc lễ hội đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope để lọc lễ hội theo người tạo
     */
    public function scopeByCreator($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Kiểm tra xem user có phải admin của lễ hội này không
     */
    public function isAdmin($userId)
    {
        return $this->created_by == $userId;
    }

    /**
     * Kiểm tra xem lễ hội có đang diễn ra không
     */
    public function isOngoing()
    {
        $now = now()->toDateString();
        return $this->is_active && 
               (!$this->start_date || $this->start_date <= $now) &&
               (!$this->end_date || $this->end_date >= $now);
    }
}
