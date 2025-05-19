<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CommentRequest;
use App\Http\Requests\Post\DestroyCommentRequest;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\LikedPost;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PostController extends BaseController
{
    public function index(): AnonymousResourceCollection
    {
        $posts = Post::latest()->get();

        $likedPostsByTargetUser = LikedPost::where('user_id', auth()->id())
            ->get('post_id')
            ->pluck('post_id')
            ->toArray();

        foreach ($posts as $post) {
            if (in_array($post->id, $likedPostsByTargetUser)) {
                $post->isLiked = true;
            }
        }

        return PostResource::collection($posts);
    }


    public function store(StoreRequest $request): PostResource|Response
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $imageId = $data['image_id'];
            unset($data['image_id']);

            $data['user_id'] = auth()->id();
            $post = Post::create($data);
            $this->attachImageToPost($post->id, $imageId);

            PostImage::removeFreeImagesFromStorage();

            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            return response(['error' => $e->getMessage()], 500);
        }

        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        try {
            DB::beginTransaction();

            $likes = $post->likes()->get();
            foreach ($likes as $like) {
                $like->delete();
            }

            $comments = $post->comments()->get();
            foreach ($comments as $comment) {
                $comment->delete();
            }

            if ($post->image) {
                $post->image->delete();
            }

            $post->delete();

            DB::commit();
        } catch(\Throwable $e) {
            DB::rollBack();
            return response(['error' => $e->getMessage()], 500);
        }

        return response(['data' => true], 200);
    }

    public function toggleLike(Post $post)
    {
        $toggleLike = Auth()->user()->likedPosts()->toggle($post->id);
        $data['is_liked'] = count($toggleLike['attached']) > 0;
        $data['likes_count'] = $post->likedUsers()->count();

        return $data;
    }

    public function comment(CommentRequest $request, Post $post)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['post_id'] = $post->id;

        $comment = Comment::create($data);

        return new CommentResource($comment);
    }


    public function commentList(Post $post)
    {
        $comments = $post->comments()->get();

        foreach ($comments as $comment) {
            if ($comment['user_id'] === auth()->id()) {
                $comment['owner'] = true;
            }
        }

        return CommentResource::collection($comments);
    }

    private function attachImageToPost($postId, $imageId): void
    {
        if (isset($imageId)) {
            $image = PostImage::find($imageId);
            $image->update(['post_id' => $postId]);
        }
    }

}
