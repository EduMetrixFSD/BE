<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // 關聯：標籤對應的課程
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_tag');
    }
}
