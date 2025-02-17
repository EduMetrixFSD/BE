<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'parent_id'];

   // 關聯：類別下的所有課程
   public function courses()
   {
       return $this->hasMany(Course::class);
   }

   // 關聯：父類別
   public function parent()
   {
       return $this->belongsTo(Category::class, 'parent_id');
   }

   // 關聯：子類別
   public function children()
   {
       return $this->hasMany(Category::class, 'parent_id');
   }
}

