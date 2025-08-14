<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
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
        'describtion',
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
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'user_exams')
            ->withPivot(['started_at', 'finished_at', 'total_time_seconds', 'status', 'answers'])
            ->withTimestamps();
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }
}
