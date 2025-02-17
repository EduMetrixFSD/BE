<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'user_id', 'rating', 'comment'];

    // 關聯：課程
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // 關聯：使用者
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
