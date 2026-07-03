<x-layouts.admin title="Dashboard — SI-Pedia" section="dashboard">

{{-- Stats row --}}

<div class="mb-6 grid gap-4 grid-cols-2 sm:grid-cols-4">
  @foreach([
    ['label'=>'Total Artikel','value'=>$stats['articles'],'icon'=>'📄','color'=>'bg-blue-50 text-blue-700','border'=>'border-blue-200'],
    ['label'=>'Dosen','value'=>$stats['lecturers'],'icon'=>'🎓','color'=>'bg-green-50 text-green-700','border'=>'border-green-200'],
    ['label'=>'Pengguna','value'=>$stats['users'],'icon'=>'👥','color'=>'bg-purple-50 text-purple-700','border'=>'border-purple-200'],
    ['label'=>'Pending','value'=>$stats['pending'],'icon'=>'⏳','color'=>'bg-yellow-50 text-yellow-700','border'=>'border-yellow-200','alert'=>true],
  ] as $stat)
  <div class="rounded-xl border {{ $stat['border'] }} bg-white p-4 shadow-sm">
    <div class="flex items-center justify-between mb-2">
      <span class="text-xl" aria-hidden="true">{{ $stat['icon'] }}</span>
      @if(($stat['alert'] ?? false) && $stat['value'] > 0)
      <span class="rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-black text-white">!</span>
      @endif
    </div>
    <p class="text-2xl font-black text-gray-900">{{ number_format($stat['value']) }}</p>
    <p class="text-xs font-semibold text-gray-500 mt-0.5">{{ $stat['label'] }}</p>
  </div>
  @endforeach
</div>

<div class="grid gap-6 lg:grid-cols-[1fr_320px]">

  {{-- Left column --}}
  <div class="space-y-6">

    {{-- Monthly chart --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
      <h2 class="mb-4 text-sm font-extrabold text-gray-800">📈 Artikel per Bulan ({{ now()->year }})</h2>
      @php
        $monthData = array_fill(1,12,0);
        foreach($monthlyArticles as $m) { $row = is_array($m) ? (object)$m : $m; if (is_object($row) && isset($row->month)) $monthData[(int)$row->month] = (int)$row->count; }
        $maxVal = max(array_values($monthData)) ?: 1;
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
      @endphp
      <div class="flex items-end gap-1.5 h-32" role="img" aria-label="Bar chart artikel per bulan">
        @foreach($monthData as $month => $count)
        <div class="flex-1 flex flex-col items-center gap-1 group">
          <span class="text-[9px] font-bold text-gray-400 opacity-0 group-hover:opacity-100 transition">{{ $count }}</span>
          <div class="w-full rounded-t-sm bg-brand-600/80 hover:bg-brand-600 transition-all cursor-default"
               style="height: {{ max(4, ($count / $maxVal) * 112) }}px"
               title="{{ $months[$month-1] }}: {{ $count }} artikel"
               role="presentation"></div>
          <span class="text-[9px] font-semibold text-gray-400">{{ $months[$month-1] }}</span>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Top articles --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
      <div class="border-b border-gray-100 bg-gray-50 px-4 py-3 flex items-center justify-between">
        <h2 class="text-sm font-extrabold text-gray-800">🔥 Artikel Terpopuler</h2>
        <a href="{{ route('admin.articles.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Lihat semua →</a>
      </div>
      <div class="divide-y divide-gray-50">
        @foreach($topArticles as $art)
        @continue(!is_object($art))
        <div class="flex items-center gap-3 px-4 py-3">
          <span class="flex-shrink-0 w-6 text-sm font-black {{ $loop->index===0?'text-yellow-500':($loop->index===1?'text-gray-400':($loop->index===2?'text-amber-600':'text-gray-300')) }}">
            {{ $loop->iteration }}
          </span>
          @if($art->image_url)
          <img src="{{ $art->image_url }}" alt="{{ $art->title }}" class="h-10 w-14 flex-shrink-0 rounded-lg object-cover">
          @endif
          <div class="flex-1 min-w-0">
            <a href="{{ route('articles.show', $art->slug) }}" target="_blank"
               class="text-sm font-semibold text-gray-900 hover:text-brand-700 transition line-clamp-1">{{ $art->title }}</a>
            <p class="text-xs text-gray-400">{{ $art->category->name ?? '-' }}</p>
          </div>
          <span class="flex-shrink-0 text-xs font-bold text-gray-500">{{ number_format($art->views) }}×</span>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Recent activity --}}
    @if($recentActivities->isNotEmpty())
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
      <div class="border-b border-gray-100 bg-gray-50 px-4 py-3">
        <h2 class="text-sm font-extrabold text-gray-800">⚡ Aktivitas Terbaru</h2>
      </div>
      <div class="divide-y divide-gray-50">
        @foreach($recentActivities->take(8) as $act)
        <div class="flex items-start gap-3 px-4 py-3">
          <img src="{{ $act->user->avatar_url ?? 'https://ui-avatars.com/api/?name=S&background=336cbc&color=fff&size=40' }}"
               alt="" class="h-8 w-8 flex-shrink-0 rounded-full object-cover" aria-hidden="true">
          <div class="flex-1 min-w-0">
            <p class="text-sm text-gray-700 line-clamp-1">
              <span class="font-semibold">{{ $act->user->name ?? 'System' }}</span>
              <span class="text-gray-400">·</span>
              {{ $act->description ?? $act->action }}
            </p>
            <time class="text-xs text-gray-400" datetime="{{ $act->created_at?->toISOString() }}">
              {{ $act->created_at?->diffForHumans() }}
            </time>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif
  </div>

  {{-- Right column --}}
  <div class="space-y-5">

    {{-- Quick actions --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
      <div class="border-b border-gray-100 bg-gray-50 px-4 py-3">
        <h2 class="text-sm font-extrabold text-gray-800">⚡ Aksi Cepat</h2>
      </div>
      <div class="divide-y divide-gray-50">
        @foreach([
          ['Tambah Artikel',   route('admin.articles.create'),          '✏️', null],
          ['Pending Artikel',  route('admin.articles.pending'),         '⏳', $stats['pending']],
          ['Moderasi Komentar',route('admin.comments.index'),           '💬', \App\Models\Comment::where('status','pending')->count()],
          ['Report Akun',      route('admin.account-reports.index'),    '🚩', \App\Models\AccountReport::where('status','pending')->count()],
          ['Manage Users',     route('admin.users.index'),              '👥', null],
          ['Tambah Dosen',     route('admin.dosen.create'),             '🎓', null],
          ['Laporan Artikel',  route('admin.report'),                   '📊', null],
        ] as [$label, $url, $icon, $badge])
        <a href="{{ $url }}" class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition group">
          <div class="flex items-center gap-2.5">
            <span class="text-base" aria-hidden="true">{{ $icon }}</span>
            <span class="text-sm font-semibold text-gray-700 group-hover:text-brand-700">{{ $label }}</span>
          </div>
          <div class="flex items-center gap-2">
            @if(!empty($badge) && $badge > 0)
            <span class="rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-black text-white" aria-label="{{ $badge }} item menunggu">{{ $badge }}</span>
            @endif
            <svg class="h-3.5 w-3.5 text-gray-300 group-hover:text-brand-600 transition" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
          </div>
        </a>
        @endforeach
      </div>
    </div>

    {{-- Top users --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
      <div class="border-b border-gray-100 bg-gray-50 px-4 py-3">
        <h2 class="text-sm font-extrabold text-gray-800">✍️ Penulis Teraktif</h2>
      </div>
      <div class="divide-y divide-gray-50">
        @foreach($topUsers as $u)
        <div class="flex items-center gap-3 px-4 py-3">
          <img src="{{ $u->avatar_url }}" alt="Foto {{ $u->name }}" class="h-8 w-8 flex-shrink-0 rounded-full object-cover">
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-900 truncate">{{ $u->name }}</p>
            <p class="text-xs text-gray-400">{{ ucfirst($u->role) }}</p>
          </div>
          <span class="text-xs font-bold text-gray-500">{{ $u->articles_count }} artikel</span>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

</x-layouts.admin>
