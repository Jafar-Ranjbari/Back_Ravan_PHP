<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstituteExam extends Model
{
    use HasFactory;

    protected $table = 'institute_exams';

    protected $fillable = [
        'institute_id',
        'exam_id',
        'describtion',
        'is_active',
    ];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
