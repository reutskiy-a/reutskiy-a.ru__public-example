<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LikedPost extends Model
{
    protected $table = 'liked_posts';
    protected $guarded = false;
}
