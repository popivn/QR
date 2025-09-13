<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
        'group_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Một user thuộc về một group
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // Quan hệ với Festivals mà user đã tạo (admin)
    public function createdFestivals()
    {
        return $this->hasMany(Festival::class, 'created_by');
    }

    // Kiểm tra quyền admin
    public function isAdmin()
    {
        return $this->role_id == 1;
    }

    // Kiểm tra xem user có phải admin của festival nào đó không
    public function isFestivalAdmin($festivalId)
    {
        return $this->createdFestivals()->where('id', $festivalId)->exists();
    }

    // Lấy danh sách festival mà user là admin
    public function getAdminFestivals()
    {
        return $this->createdFestivals()->active()->get();
    }

    /**
     * Find user by username for authentication
     */
    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }
}
