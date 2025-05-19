<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $table = 'posts';
    protected $guarded = false;
    protected $with = ['image', 'likedUsers'];
    protected $withCount = ['comments'];

    public function image()
    {
        return $this->hasOne(PostImage::class, 'post_id', 'id');
    }

    public function getDateAttribute()
    {
        return $this->created_at->locale('ru')->diffForHumans();
    }

    public function likedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'liked_posts', 'post_id', 'user_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(LikedPost::class, 'post_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

}

