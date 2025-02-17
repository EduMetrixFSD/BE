<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'title', 'video_url', 'sort_order'];

    // 關聯：課程
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
