<x-layouts.app title="Admin Panel — SI-Pedia">
<main class="mx-auto max-w-[1440px] px-8 py-7">
  <div class="flex items-start justify-between">
    <div><h1 class="text-5xl font-black tracking-tight">Admin Panel</h1>
      <p class="mt-1 text-gray-700">Welcome Admin. Manage the system easily.</p></div>
    <span class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
  </div>

  {{-- Stats Cards --}}
  <div class="mt-7 grid grid-cols-4 gap-6">
    <div class="rounded-2xl border border-gray-200 bg-white px-6 py-7 text-center shadow-sm">
      <p class="text-xl font-bold text-gray-900">Total Articles</p>
      <p class="mt-1 text-6xl font-black text-gray-900">{{ $stats['articles'] }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white px-6 py-7 text-center shadow-sm">
      <p class="text-xl font-bold text-gray-900">Total Lecturers</p>
      <p class="mt-1 text-6xl font-black text-gray-900">{{ $stats['lecturers'] }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white px-6 py-7 text-center shadow-sm">
      <p class="text-xl font-bold text-gray-900">Total Review</p>
      <p class="mt-1 text-6xl font-black text-gray-900">{{ $stats['reviews'] }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white px-6 py-7 text-center shadow-sm">
      <p class="text-xl font-bold text-gray-900">Total User</p>
      <p class="mt-1 text-6xl font-black text-gray-900">{{ $stats['users'] }}</p>
    </div>
  </div>

  {{-- Articles Chart (CSS bar chart) --}}
  @if(isset($monthlyArticles) && $monthlyArticles->count())
  <div class="mt-8 rounded-2xl border border-gray-200 p-5 shadow-sm">
    <h2 class="mb-4 text-xl font-bold">Articles per Bulan</h2>
    <div class="flex items-end gap-2 h-40">
      @php $maxCount = $monthlyArticles->max('count') ?: 1; @endphp
      @foreach($monthlyArticles as $month)
        <div class="flex-1 flex flex-col items-center gap-1">
          <span class="text-xs font-bold text-gray-600">{{ $month->count }}</span>
          <div class="w-full rounded-t-lg bg-[#336cbc] transition-all" style="height: {{ ($month->count / $maxCount) * 100 }}%"></div>
          <span class="text-[10px] text-gray-500">{{ \Carbon\Carbon::create()->month((int)$month->month)->format('M') }}</span>
        </div>
      @endforeach
    </div>
  </div>
  @endif

  <div class="mt-8 grid grid-cols-[1.7fr_1fr] gap-6">
    {{-- New Articles Table --}}
    <div class="rounded-2xl border border-gray-200 p-5 shadow-sm">
      <div class="mb-4 flex items-center justify-between"><h2 class="text-xl font-bold">New Articles</h2><a href="{{ route('admin.articles.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">See All</a></div>
      <div class="grid grid-cols-[1fr_110px_90px_130px_90px] gap-2 border-b bg-tablehead/60 px-3 py-2 text-xs font-bold text-gray-700"><div>Article Title</div><div>Category</div><div>Writer</div><div>Created Date</div><div>Status</div></div>
      <div class="mt-3 space-y-3">
        @foreach($articles as $article)
        <div class="grid grid-cols-[1fr_110px_90px_130px_90px] items-center gap-2 rounded-xl bg-white px-3 py-3 shadow-sm">
          <div class="flex items-center gap-3">
            @if($article->image)
              <img src="{{ $article->image_url }}" class="h-12 w-12 rounded object-cover" alt="{{ $article->title }}">
            @else
              <div class="h-12 w-12 rounded bg-gray-200 flex items-center justify-center text-[10px] text-gray-500">No Img</div>
            @endif
            <span class="text-xs font-bold leading-tight">{{ $article->title }}</span>
          </div>
          <div><span class="rounded-full bg-badge-cat px-4 py-1 text-xs font-semibold text-white">{{ $article->category->name ?? 'Uncategorized' }}</span></div>
          <div class="text-xs font-bold">{{ $article->writer }}</div>
          <div class="text-xs font-bold">{{ \Carbon\Carbon::parse($article->created_at)->translatedFormat('j F Y') }}</div>
          <div><span class="rounded-md {{ $article->status === 'active' ? 'bg-status-active' : 'bg-red-500' }} px-3 py-1 text-xs font-semibold text-white">{{ ucfirst($article->status) }}</span></div>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Fast Action --}}
    <div class="rounded-2xl border border-gray-200 p-5 shadow-sm">
      <h2 class="mb-4 text-xl font-bold">Fast Action</h2>
      <div class="space-y-3">
        <a href="{{ route('admin.articles.create') }}" class="flex items-center justify-between rounded-xl bg-white px-5 py-4 shadow-sm hover:bg-gray-50 transition-colors">
          <div><p class="font-bold text-gray-900">Add Article</p><p class="text-xs text-gray-500">Create a New Article</p></div>
          <span class="text-gray-400">›</span>
        </a>
        <a href="{{ route('admin.categories.index') }}" class="flex items-center justify-between rounded-xl bg-white px-5 py-4 shadow-sm hover:bg-gray-50 transition-colors">
          <div><p class="font-bold text-gray-900">Manage Category</p><p class="text-xs text-gray-500">Add or Edit Category</p></div>
          <span class="text-gray-400">›</span>
        </a>
        <a href="{{ route('admin.dosen.index') }}" class="flex items-center justify-between rounded-xl bg-white px-5 py-4 shadow-sm hover:bg-gray-50 transition-colors">
          <div><p class="font-bold text-gray-900">Manage Lecturers</p><p class="text-xs text-gray-500">View and Manage Lecturer Data</p></div>
          <span class="text-gray-400">›</span>
        </a>
        <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between rounded-xl bg-white px-5 py-4 shadow-sm hover:bg-gray-50 transition-colors">
          <div><p class="font-bold text-gray-900">Manage Users</p><p class="text-xs text-gray-500">View and Manage Users</p></div>
          <span class="text-gray-400">›</span>
        </a>
        <a href="{{ route('admin.report') }}" class="flex items-center justify-between rounded-xl bg-white px-5 py-4 shadow-sm hover:bg-gray-50 transition-colors">
          <div><p class="font-bold text-gray-900">Report Posts</p><p class="text-xs text-gray-500">View Post Report</p></div>
          <span class="text-gray-400">›</span>
        </a>
        <a href="{{ route('admin.account-reports.index') }}" class="flex items-center justify-between rounded-xl bg-white px-5 py-4 shadow-sm hover:bg-gray-50 transition-colors">
          <div><p class="font-bold text-gray-900">Report Akun</p><p class="text-xs text-gray-500">Tinjau laporan pengguna</p></div>
          @php $rCount = \App\Models\AccountReport::where('status','pending')->count(); @endphp
          @if($rCount > 0)
            <span class="rounded-full bg-red-500 px-2 py-0.5 text-xs font-bold text-white">{{ $rCount }}</span>
          @else
            <span class="text-gray-400">›</span>
          @endif
        </a>
        <a href="{{ route('admin.articles.pending') }}" class="flex items-center justify-between rounded-xl bg-white px-5 py-4 shadow-sm hover:bg-gray-50 transition-colors">
          <div><p class="font-bold text-gray-900">Artikel Pending</p><p class="text-xs text-gray-500">Approve / reject artikel masuk</p></div>
          @if(isset($stats['pending']) && $stats['pending'] > 0)
            <span class="rounded-full bg-yellow-400 px-2 py-0.5 text-xs font-bold text-white">{{ $stats['pending'] }}</span>
          @else
            <span class="text-gray-400">›</span>
          @endif
        </a>
      </div>
    </div>
  </div>

  {{-- Activity Log --}}
  @if(isset($recentActivities) && $recentActivities->count())
  <div class="mt-8 rounded-2xl border border-gray-200 p-5 shadow-sm">
    <h2 class="mb-4 text-xl font-bold">Recent Activity</h2>
    <div class="space-y-2">
      @foreach($recentActivities as $log)
      <div class="flex items-center gap-3 rounded-lg bg-gray-50 px-4 py-2.5">
        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#336cbc] text-white flex items-center justify-center text-xs font-bold">
          {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm text-gray-800">
            <span class="font-semibold">{{ $log->user->name ?? 'System' }}</span>
            {{ $log->description }}
          </p>
          <p class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }} · {{ $log->ip_address }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif
</main>
</x-layouts.app>
