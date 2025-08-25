<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'laterality',
    ];

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'exam_package');
    }

    public function requestItems()
    {
        return $this->hasMany(ExamRequestItem::class);
    }
}
