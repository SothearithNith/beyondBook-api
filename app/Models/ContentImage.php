<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentImage extends Model
{
    use HasFactory;
    protected $table = 'tbl_content_images';

    protected $fillable = ['content_id', 'image_path'];

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }
}
