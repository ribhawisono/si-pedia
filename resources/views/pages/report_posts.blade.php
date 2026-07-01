<x-layouts.admin title="Laporan Artikel — SI-Pedia" section="analytics">
<main class="mx-auto max-w-[1440px] px-8 py-7">
  <div class="flex items-start justify-between">
    <div><h1 class="text-5xl font-black tracking-tight">Report Posts</h1>
      <p class="mt-1 text-gray-700">Reports and Statistics of All Article Posts on the System.</p></div>
    <div class="flex gap-3"><span class="flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold">01 - {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span></div>
  </div>

  {{-- Stats Cards --}}
  <div class="mt-7 grid grid-cols-5 gap-6">
    <div class="rounded-2xl border border-gray-200 bg-white px-6 py-7 text-center shadow-sm">
      <p class="text-xl font-bold text-gray-900">Total Posts</p>
      <p class="mt-1 text-6xl font-black text-gray-900">{{ $stats['total'] }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white px-6 py-7 text-center shadow-sm">
      <p class="text-xl font-bold text-gray-900">Active Posts</p>
      <p class="mt-1 text-6xl font-black text-green-600">{{ $stats['active'] }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white px-6 py-7 text-center shadow-sm">
      <p class="text-xl font-bold text-gray-900">Draft Post</p>
      <p class="mt-1 text-6xl font-black text-yellow-600">{{ $stats['draft'] }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white px-6 py-7 text-center shadow-sm">
      <p class="text-xl font-bold text-gray-900">Post Deleted</p>
      <p class="mt-1 text-6xl font-black text-red-600">{{ $stats['deleted'] }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white px-6 py-7 text-center shadow-sm">
      <p class="text-xl font-bold text-gray-900">Scheduled</p>
      <p class="mt-1 text-6xl font-black text-blue-600">{{ $stats['scheduled'] }}</p>
    </div>
  </div>

  <div class="mt-8 grid grid-cols-[1.7fr_1fr] gap-6">
    {{-- Articles Table --}}
    <div class="rounded-2xl border border-gray-200 p-5 shadow-sm">
      <div class="mb-4 flex items-center justify-between"><h2 class="text-xl font-bold">New Post</h2><a href="{{ route('admin.articles.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">See All</a></div>
      <div class="grid grid-cols-[40px_1fr_90px_70px_120px_80px_70px] gap-2 bg-tablehead/60 px-3 py-2 text-[11px] font-bold text-gray-700"><div>No</div><div>Article Title</div><div>Category</div><div>Writer</div><div>Created Date</div><div>Status</div><div>Views</div></div>
      <div class="mt-3 space-y-3">
        @foreach($articles as $i => $article)
        <div class="grid grid-cols-[40px_1fr_90px_70px_120px_80px_70px] items-center gap-2 rounded-xl bg-white px-3 py-3 shadow-sm">
          <div class="text-sm font-bold">{{ $i + 1 }}</div>
          <div class="flex items-center gap-2">
            @if($article->image)
              <img src="{{ $article->image_url }}" class="h-11 w-11 rounded object-cover" alt="{{ $article->title }}">
            @else
              <div class="h-11 w-11 rounded bg-gray-200 flex items-center justify-center text-[10px] text-gray-500">No Img</div>
            @endif
            <span class="text-[11px] font-bold leading-tight">{{ Str::limit($article->title, 60) }}</span>
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

    <div class="space-y-5">
      {{-- Summary --}}
      <div class="rounded-2xl border border-gray-200 p-4 shadow-sm">
        <h3 class="mb-3 rounded-lg bg-tablehead px-3 py-2 text-lg font-bold">Summary</h3>
        <div class="space-y-2">
          @php
            $avgPerDay = $stats['total'] > 0 ? round($stats['total'] / max(1, \Carbon\Carbon::now()->diffInDays(\App\Models\Article::oldest()->first()?->created_at ?? now())), 1) : 0;
            $topWriter = \App\Models\Article::selectRaw('writer, COUNT(*) as count')->groupBy('writer')->orderByDesc('count')->first();
            $topCategory = \App\Models\Category::withCount('articles')->orderByDesc('articles_count')->first();
          @endphp
          <div class="flex items-center justify-between rounded-lg bg-white px-4 py-3 shadow-sm">
            <span class="text-sm text-gray-700">Average Posts/Day</span>
            <span class="text-sm font-bold">{{ $avgPerDay }}</span>
          </div>
          <div class="flex items-center justify-between rounded-lg bg-white px-4 py-3 shadow-sm">
            <span class="text-sm text-gray-700">Most Writers</span>
            <span class="text-sm font-bold">{{ $topWriter?->writer ?? '-' }} ({{ $topWriter?->count ?? 0 }})</span>
          </div>
          <div class="flex items-center justify-between rounded-lg bg-white px-4 py-3 shadow-sm">
            <span class="text-sm text-gray-700">Most Categories</span>
            <span class="text-sm font-bold">{{ $topCategory?->name ?? '-' }} ({{ $topCategory?->articles_count ?? 0 }})</span>
          </div>
        </div>
      </div>

      {{-- Recent Activity --}}
      @if(isset($recentActivities) && $recentActivities->count())
      <div class="rounded-2xl border border-gray-200 p-4 shadow-sm">
        <h3 class="mb-3 rounded-lg bg-tablehead px-3 py-2 text-lg font-bold">Recent Activity</h3>
        <div class="space-y-2 max-h-80 overflow-y-auto">
          @foreach($recentActivities as $log)
          <div class="rounded-lg bg-white px-3 py-2.5 shadow-sm">
            <p class="text-xs text-gray-800">
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
