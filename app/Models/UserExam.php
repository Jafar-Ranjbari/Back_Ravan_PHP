<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExam extends Model
{
    use HasFactory;

    protected $table = 'user_exams';

    protected $fillable = [
        'user_id',
        'exam_id',
        'started_at',
        'finished_at',
        'total_time_seconds',
        'status',
        'answers',
        'describtion',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
