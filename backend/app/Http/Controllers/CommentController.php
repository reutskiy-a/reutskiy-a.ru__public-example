<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CommentRequest;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController
{

    public function destroy(Comment $comment)
    {
        $result = $comment->delete();
        return response(['data' => $result], 200);
    }

    public function patch(CommentRequest $request, Comment $comment)
    {
        $data = $request->validated();
        $result = $comment->update($data);

        return response(['data' => $result], 200);
    }
}
