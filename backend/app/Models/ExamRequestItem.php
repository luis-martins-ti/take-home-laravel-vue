<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_request_id',
        'exam_id',
        'laterality',
        'comment',
        'group',
        'package_id',
    ];

    public function request()
    {
        return $this->belongsTo(ExamRequest::class, 'exam_request_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
