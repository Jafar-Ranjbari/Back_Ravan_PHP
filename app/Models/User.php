<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'mobile',
        'sex',
        'age',
        'password',
        'role_id',
        'institute_id',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'age' => 'integer',
        'role_id' => 'integer',
        'institute_id' => 'integer',
    ];

    /**
     * Get the user's role.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user's institute.
     */
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * Get the user's exams.
     */
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'user_exams')
            ->withPivot(['started_at', 'finished_at', 'total_time_seconds', 'status', 'answers'])
            ->withTimestamps();
    }

    /**
     * Get the user's exam results.
     */
    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Get the user's full name attribute.
     */
    public function getFullNameAttribute($value)
    {
        return ucwords($value);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Check if user is active.
     */
    public function isActive()
    {
        return $this->is_active === true;
    }
}
