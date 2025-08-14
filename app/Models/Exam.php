<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'question_count',
        'price',
        'link',
        'duration_minutes',
        'discount_percent',
        'quiz_type',
        'description',
        'image_url',
        'describtion',
        'is_active',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_exams')
            ->withPivot(['started_at', 'finished_at', 'total_time_seconds', 'status', 'answers'])
            ->withTimestamps();
    }

    public function institutes()
    {
        return $this->belongsToMany(Institute::class, 'institute_exams')
            ->withTimestamps();
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }
}
