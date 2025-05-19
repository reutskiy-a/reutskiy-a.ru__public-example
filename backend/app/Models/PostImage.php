<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PostImage extends Model
{
    protected $table = 'post_images';
    protected $guarded = false;

    public function getUrlAttribute()
    {
        return url('storage/' . $this->path);
    }

    public function deleteImageFile(): bool
    {
        if ($this->path) {
            return Storage::disk('public')->delete($this->path);
        }

        return false;
    }

    public function delete(): bool|null
    {
        $this->deleteImageFile();
        return parent::delete();
    }

    public static function removeFreeImagesFromStorage(): void
    {
        $images = PostImage::where('user_id', auth()->id())
            ->whereNull('post_id')->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
    }
}
