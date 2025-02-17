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
        'price',
        'cover_image',
        'teacher_id',
        'category_id',
        'subcategory_id',
        'status'
    ];
    // 關聯：課程的老師
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // 關聯：課程分類
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // 關聯：課程的章節
    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    // 關聯：課程的標籤
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'course_tag');
    }

    // 關聯：課程的訂單項目
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
