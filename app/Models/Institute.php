<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'logo_url',
        'start_date',
        'end_date',
        'mobile',
        'username',
        'password',
        'describtion',
        'is_active',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'institute_exams')
            ->withTimestamps();
    }
}
