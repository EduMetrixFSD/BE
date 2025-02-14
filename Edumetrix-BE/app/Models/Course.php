<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'cover_image',
        'teacher_id',
        'category_id',
        'subcategory_id',
        'rating',
        'rating_count',
        'purchase_count',
        'price'
    ];

    // 關聯：每門課程對應一位講師
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    // 關聯：課程的大類別
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // 關聯：課程的小類別
    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }
    // 關聯：課程的標籤
    public function hashtags()
{
    return $this->belongsToMany(Hashtag::class, 'course_hashtags', 'course_id', 'hashtag_id');
}
}
