<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Article;
use App\Models\BannedWord;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /** PUBLIC: list approved comments for one specific article. Bound to
     *  GET /articles/{article:slug}/comments (no auth required). This used
     *  to incorrectly route to the admin moderation method below (which had
     *  no role check at all) — any visitor could browse to that URL and see
     *  the full admin comment-moderation dashboard for the whole site. */
    public function index(Article $article)
    {
        $comments = $article->comments()
            ->where('status', 'approved')
            ->with('user:id,name,avatar')
            ->latest()
            ->paginate(20);

        return view('pages.article_comments', compact('article', 'comments'));
    }

    /** Admin: list all comments for moderation (auth+admin middleware) */
    public function adminIndex(Request $request)
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

        $bannedWords = BannedWord::orderBy('word')->get();

        return view('pages.admin_comments', compact('comments', 'counts', 'bannedWords'));
    }

    /**
     * Submit comment. Comments post immediately (status 'approved') like a
     * normal comment box — EXCEPT when the content matches an entry in the
     * banned-word list (managed by admin, see banned words CRUD below), in
     * which case it's held as 'pending' for manual review instead of going
     * straight onto the article. Admin's own comments always auto-approve
     * regardless (skip the filter entirely).
     */
    public function store(StoreCommentRequest $request, Article $article)
    {
        $data    = $request->validated();
        $isAdmin = $request->user()->role === 'admin';

        $flagged = !$isAdmin && BannedWord::containsBannedWord($data['content']);

        $article->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $data['content'],
            'status'  => $isAdmin ? 'approved' : ($flagged ? 'pending' : 'approved'),
        ]);

        return back()->with('success', $flagged
            ? 'Komentar terkirim, namun mengandung kata yang perlu ditinjau admin sebelum tampil.'
            : 'Komentar ditambahkan.'
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

    /** Admin: tambah kata terlarang baru ke daftar filter */
    public function storeBannedWord(Request $request)
    {
        $data = $request->validate([
            'word' => 'required|string|max:100|unique:banned_words,word',
        ]);

        BannedWord::create([
            'word'       => mb_strtolower(trim($data['word'])),
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Kata terlarang ditambahkan.');
    }

    /** Admin: hapus kata terlarang dari daftar filter */
    public function destroyBannedWord(BannedWord $bannedWord)
    {
        $bannedWord->delete();
        return back()->with('success', 'Kata terlarang dihapus.');
    }
}
