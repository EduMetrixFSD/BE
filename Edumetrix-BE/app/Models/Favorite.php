<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'course_id'];

    // 關聯：使用者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 關聯：課程
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
