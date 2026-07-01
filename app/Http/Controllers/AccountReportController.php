<?php

namespace App\Http\Controllers;

use App\Models\AccountReport;
use App\Models\User;
use Illuminate\Http\Request;

class AccountReportController extends Controller
{
    // Form report akun (user login)
    public function create(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kamu tidak bisa melaporkan akunmu sendiri.');
        }
        return view('pages.report_account_form', compact('user'));
    }

    // Simpan laporan
    public function store(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kamu tidak bisa melaporkan akunmu sendiri.');
        }

        // Cegah report duplikat dari user yang sama ke target yang sama jika masih pending
        $existing = AccountReport::where('reporter_id', auth()->id())
            ->where('reported_user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return back()->with('error', 'Kamu sudah pernah melaporkan akun ini dan laporanmu masih dalam proses review.');
        }

        $data = $request->validate([
            'reason'      => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        AccountReport::create([
            'reporter_id'      => auth()->id(),
            'reported_user_id' => $user->id,
            'reason'           => $data['reason'],
            'description'      => $data['description'] ?? null,
            'status'           => 'pending',
        ]);

        return redirect()->back()->with('success', 'Laporan berhasil dikirim. Admin akan meninjau laporan ini.');
    }

    // Admin: daftar semua laporan
    public function index(Request $request)
    {
        $reports = AccountReport::with(['reporter', 'reportedUser'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15);

        $counts = [
            'pending'   => AccountReport::where('status', 'pending')->count(),
            'reviewed'  => AccountReport::where('status', 'reviewed')->count(),
            'dismissed' => AccountReport::where('status', 'dismissed')->count(),
        ];

        return view('pages.admin_account_reports', compact('reports', 'counts'));
    }

    // Admin: update status laporan
    public function update(Request $request, AccountReport $report)
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
