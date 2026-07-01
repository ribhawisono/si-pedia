<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\{Article, Comment};
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Article $article)
    {
        $comments = $article->comments()
            ->with('user:id,name,avatar')
            ->where('status', 'approved')
            ->paginate(20);
        return CommentResource::collection($comments);
    }

    public function store(Request $request, Article $article)
    {
        $data = $request->validate(['content' => 'required|string|max:1000']);
        $comment = $article->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $data['content'],
            'status'  => 'pending',
        ]);
        return (new CommentResource($comment->load('user:id,name,avatar')))
            ->response()->setStatusCode(201)
            ->additional(['message' => 'Komentar menunggu moderasi admin.']);
    }
}
