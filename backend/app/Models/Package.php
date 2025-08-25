<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'observations',
    ];

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_package');
    }

    public function requestItems()
    {
        return $this->hasMany(ExamRequestItem::class);
    }
}
