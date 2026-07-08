<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleReport;
use Illuminate\Http\Request;

class ArticleReportController extends Controller
{
    // Form report artikel (user login)
    public function create(Article $article)
    {
        if ($article->user_id === auth()->id()) {
            return back()->with('error', 'Kamu tidak bisa melaporkan artikelmu sendiri.');
        }

        return view('pages.report_article_form', compact('article'));
    }

    // Simpan laporan
    public function store(Request $request, Article $article)
    {
        if ($article->user_id === auth()->id()) {
            return back()->with('error', 'Kamu tidak bisa melaporkan artikelmu sendiri.');
        }

        // Cegah report duplikat dari user yang sama ke artikel yang sama jika masih pending
        $existing = ArticleReport::where('reporter_id', auth()->id())
            ->where('article_id', $article->id)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return back()->with('error', 'Kamu sudah pernah melaporkan artikel ini dan laporanmu masih dalam proses review.');
        }

        $data = $request->validate([
            'reason'      => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        ArticleReport::create([
            'reporter_id' => auth()->id(),
            'article_id'  => $article->id,
            'reason'      => $data['reason'],
            'description' => $data['description'] ?? null,
            'status'      => 'pending',
        ]);

        return redirect()->route('articles.show', $article->slug)->with('success', 'Laporan berhasil dikirim. Admin akan meninjau laporan ini.');
    }

    // Admin: daftar semua laporan
    public function index(Request $request)
    {
        $reports = ArticleReport::with(['reporter', 'article'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15);

        $counts = [
            'pending'   => ArticleReport::where('status', 'pending')->count(),
            'reviewed'  => ArticleReport::where('status', 'reviewed')->count(),
            'dismissed' => ArticleReport::where('status', 'dismissed')->count(),
        ];

        return view('pages.admin_article_reports', compact('reports', 'counts'));
    }

    // Admin: update status laporan
    public function update(Request $request, ArticleReport $report)
    {
        $data = $request->validate([
            'status'     => 'required|in:reviewed,dismissed',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $report->update($data);

        $msg = $data['status'] === 'reviewed'
            ? 'Laporan ditandai sebagai sudah ditinjau.'
            : 'Laporan berhasil diabaikan.';

        return back()->with('success', $msg);
    }
}
