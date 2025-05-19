<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostImage\StoreRequest;
use App\Http\Resources\PostImageResource;
use App\Models\PostImage;
use Illuminate\Support\Facades\Storage;


class PostImageController extends BaseController
{
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $path = Storage::disk('public')->put('/images', $data['image']);

        $image = PostImage::create([
            'path' => $path,
            'user_id' => auth()->id()
        ]);

        return new PostImageResource($image);
    }
}
