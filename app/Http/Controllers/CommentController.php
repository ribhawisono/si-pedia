<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /** Admin: list all comments for moderation */
    public function index(Request $request)
    {
        $comments = Comment::with(['article:id,title,slug', 'user:id,name'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20);

        $counts = [
            'pending'  => Comment::where('status', 'pending')->count(),
            'approved' => Comment::where('status', 'approved')->count(),
            'rejected' => Comment::where('status', 'rejected')->count(),
        ];

        return view('pages.admin_comments', compact('comments', 'counts'));
    }

    /** Submit comment. Admin comments are auto-approved (skip moderation)
     *  and show up on the article immediately; everyone else's comment
     *  goes to pending for admin review as before. */
    public function store(StoreCommentRequest $request, Article $article)
    {
        $data    = $request->validated();
        $isAdmin = $request->user()->role === 'admin';

        $article->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $data['content'],
            'status'  => $isAdmin ? 'approved' : 'pending',
        ]);

        return back()->with('success', $isAdmin
            ? 'Komentar ditambahkan.'
            : 'Komentar dikirim dan menunggu moderasi admin.'
        );
    }

    /** Admin: approve */
    public function approve(Comment $comment)
    {
        $comment->update(['status' => 'approved']);
        return back()->with('success', 'Komentar disetujui.');
    }

    /** Admin: reject */
    public function reject(Comment $comment)
    {
        $comment->update(['status' => 'rejected']);
        return back()->with('success', 'Komentar ditolak.');
    }

    /** Admin: delete */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Komentar dihapus.');
    }

    /** Admin: bulk action */
    public function bulk(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:comments,id',
            'action' => 'required|in:approve,reject,delete',
        ]);

        match ($request->action) {
            'approve' => Comment::whereIn('id', $request->ids)->update(['status' => 'approved']),
            'reject'  => Comment::whereIn('id', $request->ids)->update(['status' => 'rejected']),
            'delete'  => Comment::whereIn('id', $request->ids)->delete(),
        };

        return back()->with('success', 'Bulk action selesai.');
    }
}
