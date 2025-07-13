<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $table = 'tbl_content';
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'title_kh',
        'title_en',
        'description_kh',
        'description_en',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(ContentImage::class, 'content_id');
    }
}
