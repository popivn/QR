<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'action',
        'resource_type',
        'resource_id',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Quan hệ với User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tạo audit log entry
     */
    public static function log(string $action, ?string $resourceType = null, ?int $resourceId = null, ?string $description = null, array $metadata = []): self
    {
        $user = auth()->user();
        $request = request();

        return self::create([
            'user_id' => $user ? $user->id : null,
            'ip_address' => $user ? null : $request->ip(), // Chỉ lưu IP nếu chưa đăng nhập
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'description' => $description,
            'metadata' => array_merge($metadata, [
                'user_agent' => $request->userAgent(),
                'referer' => $request->header('referer'),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]),
        ]);
    }

    /**
     * Scope để lọc theo user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope để lọc theo action
     */
    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope để lọc theo resource type
     */
    public function scopeForResourceType($query, $resourceType)
    {
        return $query->where('resource_type', $resourceType);
    }

    /**
     * Scope để lọc theo IP address
     */
    public function scopeForIpAddress($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }
}
