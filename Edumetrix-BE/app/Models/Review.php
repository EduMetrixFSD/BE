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

    // 自動更新average_rating (boot方法)
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($review) {
            self::updateCourseRating($review->course_id);
        });

        static::deleted(function ($review) {
            self::updateCourseRating($review->course_id);
        });
    }

    private static function updateCourseRating($courseId)
    {
        $average = Review::where('course_id', $courseId)->avg('rating') ?? 0;
        Course::where('id', $courseId)->update(['average_rating' => round($average, 1)]);
    }
}
