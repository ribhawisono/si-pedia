<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::query();

        // Publik cuma boleh lihat testimoni yang sudah disetujui admin.
        // Sebelumnya tidak ada filter status sama sekali, jadi review
        // 'pending'/'declined' ikut tampil ke semua pengunjung. Admin tetap
        // lihat semua status di halaman yang sama supaya bisa moderasi.
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            $query->where('status', 'accepted');
        }

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->q}%")
                  ->orWhere('description', 'like', "%{$request->q}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $reviews = $query->latest()->paginate(8)->appends($request->query());

        return view('pages.review', compact('reviews'));
    }

    public function create()
    {
        return view('pages.review_create');
    }

    public function store(StoreReviewRequest $request)
    {
        $data = $request->validated();

        Review::create([
            'title'       => $data['title'],
            'type'        => $data['type'],
            'description' => $data['description'],
            'status'      => 'pending',
            'reviewed_at' => now()->toDateString(),
        ]);

        return redirect()->route('review.index')->with('success', 'Terima kasih! Testimoni kamu sudah dikirim dan akan ditampilkan setelah ditinjau oleh admin.');
    }

    public function accept(Review $review)
    {
        $review->update([
            'status'      => 'accepted',
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Review accepted successfully.');
    }

    public function decline(Review $review)
    {
        $review->update([
            'status'      => 'declined',
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Review declined successfully.');
    }
}
