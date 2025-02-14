<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // 與課程的多對多關係
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_hashtags', 'hashtag_id', 'course_id');
    }

}
