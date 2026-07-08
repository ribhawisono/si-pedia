<x-layouts.admin title="Laporan Artikel — SI-Pedia" section="analytics">
<main class="mx-auto max-w-[1440px] px-4 sm:px-8 py-5 sm:py-7">
  <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
    <div>
      <h1 class="page-title">Laporan Artikel</h1>
      <p class="page-subtitle">Statistik dan laporan seluruh artikel di sistem.</p>
    </div>
    <div class="flex gap-3"><span class="flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold whitespace-nowrap">01 - {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span></div>
  </div>

  {{-- Stats Cards --}}
  <div class="mt-5 sm:mt-7 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
    <div class="stat-card text-center">
      <p class="stat-card-label">Total Posts</p>
      <p class="stat-card-num mt-1 text-gray-900">{{ $stats['total'] }}</p>
    </div>
    <div class="stat-card text-center">
      <p class="stat-card-label">Active Posts</p>
      <p class="stat-card-num mt-1 text-green-600">{{ $stats['active'] }}</p>
    </div>
    <div class="stat-card text-center">
      <p class="stat-card-label">Draft Post</p>
      <p class="stat-card-num mt-1 text-yellow-600">{{ $stats['draft'] }}</p>
    </div>
    <div class="stat-card text-center">
      <p class="stat-card-label">Post Deleted</p>
      <p class="stat-card-num mt-1 text-red-600">{{ $stats['deleted'] }}</p>
    </div>
    <div class="stat-card text-center">
      <p class="stat-card-label">Scheduled</p>
      <p class="stat-card-num mt-1 text-blue-600">{{ $stats['scheduled'] }}</p>
    </div>
  </div>

  <div class="mt-6 sm:mt-8 grid grid-cols-1 lg:grid-cols-[1.7fr_1fr] gap-6">
    {{-- Articles Table --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm overflow-x-auto">
      <div class="mb-4 flex items-center justify-between"><h2 class="text-sm font-extrabold text-gray-800">📄 Artikel Terbaru</h2><a href="{{ route('admin.articles.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Lihat semua →</a></div>
      <div class="min-w-[640px]">
        <div class="grid grid-cols-[40px_1fr_90px_70px_120px_80px_70px] gap-2 bg-tablehead/60 px-3 py-2 text-[11px] font-bold text-gray-700"><div>No</div><div>Article Title</div><div>Category</div><div>Writer</div><div>Created Date</div><div>Status</div><div>Views</div></div>
        <div class="mt-3 space-y-3">
          @foreach($articles as $i => $article)
          <div class="grid grid-cols-[40px_1fr_90px_70px_120px_80px_70px] items-center gap-2 rounded-xl bg-white px-3 py-3 shadow-sm">
            <div class="text-sm font-bold">{{ $i + 1 }}</div>
            <div class="flex items-center gap-2 min-w-0">
              @if($article->image)
                <img src="{{ $article->image_url }}" class="h-11 w-11 rounded object-cover flex-shrink-0" alt="{{ $article->title }}">
              @else
                <div class="h-11 w-11 rounded bg-gray-200 flex items-center justify-center text-[10px] text-gray-500 flex-shrink-0">No Img</div>
              @endif
              <span class="text-[11px] font-bold leading-tight break-words min-w-0">{{ Str::limit($article->title, 60) }}</span>
            </div>
            <div><span class="rounded-full bg-badge-cat px-4 py-1 text-xs font-semibold text-white">{{ $article->category->name ?? 'Uncategorized' }}</span></div>
            <div class="text-[11px] font-bold">{{ $article->writer }}</div>
            <div class="text-[11px] font-bold">{{ \Carbon\Carbon::parse($article->created_at)->translatedFormat('j F Y') }}</div>
            <div><span class="rounded-md {{ $article->status === 'active' ? 'bg-status-active' : 'bg-red-500' }} px-3 py-1 text-xs font-semibold text-white">{{ ucfirst($article->status) }}</span></div>
            <div class="text-[11px] text-gray-600">👁 {{ $article->views ?? 0 }}</div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="space-y-5">
      {{-- Summary --}}
      <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-gray-50 px-4 py-3"><h3 class="text-sm font-extrabold text-gray-800">📊 Summary</h3></div>
        <div class="divide-y divide-gray-50">
          @php
            $avgPerDay = $stats['total'] > 0 ? round($stats['total'] / max(1, \Carbon\Carbon::now()->diffInDays(\App\Models\Article::oldest()->first()?->created_at ?? now())), 1) : 0;
            $topWriter = \App\Models\Article::selectRaw('writer, COUNT(*) as count')->groupBy('writer')->orderByDesc('count')->first();
            $topCategory = \App\Models\Category::withCount(['articles' => fn ($q) => $q->where('status', 'active')])->orderByDesc('articles_count')->first();
          @endphp
          <div class="flex items-center justify-between px-4 py-3">
            <span class="text-sm text-gray-500">Average Posts/Day</span>
            <span class="text-sm font-bold text-gray-800">{{ $avgPerDay }}</span>
          </div>
          <div class="flex items-center justify-between px-4 py-3">
            <span class="text-sm text-gray-500">Most Writers</span>
            <span class="text-sm font-bold text-gray-800">{{ $topWriter?->writer ?? '-' }} ({{ $topWriter?->count ?? 0 }})</span>
          </div>
          <div class="flex items-center justify-between px-4 py-3">
            <span class="text-sm text-gray-500">Most Categories</span>
            <span class="text-sm font-bold text-gray-800">{{ $topCategory?->name ?? '-' }} ({{ $topCategory?->articles_count ?? 0 }})</span>
          </div>
        </div>
      </div>

      {{-- Recent Activity --}}
      @if(isset($recentActivities) && $recentActivities->count())
      <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-gray-50 px-4 py-3"><h3 class="text-sm font-extrabold text-gray-800">⚡ Recent Activity</h3></div>
        <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
          @foreach($recentActivities as $log)
          <div class="px-4 py-3">
            <p class="text-xs text-gray-700">
              <span class="font-semibold">{{ $log->user->name ?? 'System' }}</span>
              {{ $log->description }}
            </p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
          </div>
          @endforeach
        </div>
      </div>
      @endif
    </div>
  </div>
</main>
</x-layouts.admin>
