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
