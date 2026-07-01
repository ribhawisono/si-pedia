<?php

namespace App\Http\Controllers;

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

    /** User: submit comment (goes to pending) */
    public function store(Request $request, Article $article)
    {
        $data = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $article->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $data['content'],
            'status'  => 'pending', // require admin moderation
        ]);

        return back()->with('success', 'Komentar dikirim dan menunggu moderasi admin.');
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
