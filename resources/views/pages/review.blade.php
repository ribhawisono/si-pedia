<x-layouts.app title="Review — SI-Pedia" active="Review" footer="full">
<main class="mx-auto max-w-[1440px] px-10 py-8">
  <div class="flex items-center gap-8">
    <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg text-gray-800 hover:text-brand-600 transition">← Back</a>
    <div class="flex-1">
      <form action="{{ route('review.index') }}" method="GET">
        <div class="flex h-12 items-center rounded-full bg-gray-100 px-6">
          <span class="text-gray-500">🔍</span>
          <input type="text" name="q" value="{{ request('q') }}" placeholder="Search reviews..." class="ml-2 flex-1 bg-transparent border-none focus:ring-0 text-gray-800">
        </div>
      </form>
    </div>
  </div>

  <div class="mt-6 flex gap-4">
    <a href="{{ route('review.index') }}" class="rounded-full {{ !request('type') ? 'bg-brand-300/40 text-brand-700 ring-1 ring-brand-300' : 'text-gray-500 ring-1 ring-gray-300' }} px-6 py-1.5 text-sm font-medium transition">All</a>
    <a href="{{ route('review.index', ['type' => 'article']) }}" class="rounded-full {{ request('type') === 'article' ? 'bg-brand-300/40 text-brand-700 ring-1 ring-brand-300' : 'text-gray-500 ring-1 ring-gray-300' }} px-6 py-1.5 text-sm font-medium transition">Articles</a>
    <a href="{{ route('review.index', ['type' => 'project']) }}" class="rounded-full {{ request('type') === 'project' ? 'bg-brand-300/40 text-brand-700 ring-1 ring-brand-300' : 'text-gray-500 ring-1 ring-gray-300' }} px-6 py-1.5 text-sm font-medium transition">Projects</a>
  </div>

  <div class="mt-7 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    @forelse($reviews as $review)
    <div class="rounded-2xl bg-gray-100 p-3 shadow-md hover:shadow-lg transition-shadow">
      <div class="relative">
        @if($review->image)
          <img src="{{ $review->image ? (str_starts_with($review->image, "http") ? $review->image : Storage::url($review->image)) : null }}" class="h-[150px] w-full rounded-xl object-cover" loading="lazy" alt="{{ $review->title }}">
        @else
          <div class="h-[150px] w-full rounded-xl bg-gray-300 flex items-center justify-center text-gray-500">No Image</div>
        @endif
        @if($review->avatar)
          <img src="{{ Storage::url($review->avatar) }}" class="absolute -bottom-6 left-1/2 h-12 w-12 -translate-x-1/2 rounded-full border-2 border-white object-cover" alt="Avatar">
        @else
          <div class="absolute -bottom-6 left-1/2 h-12 w-12 -translate-x-1/2 rounded-full border-2 border-white bg-gray-400 flex items-center justify-center text-white font-bold">{{ substr($review->title, 0, 1) }}</div>
        @endif
      </div>
      <div class="mt-8 text-center">
        <p class="font-semibold text-gray-900">{{ Str::limit($review->title, 30) }}</p>
        <p class="text-xs text-gray-500">{{ ucfirst($review->type ?? 'Review') }}</p>

        {{-- Status Badge --}}
        @php
          $statusColors = [
              'pending' => 'bg-yellow-100 text-yellow-700',
              'accepted' => 'bg-green-100 text-green-700',
              'declined' => 'bg-red-100 text-red-700',
          ];
        @endphp
        <span class="mt-2 inline-block rounded-full px-3 py-0.5 text-[10px] font-semibold {{ $statusColors[$review->status] ?? 'bg-gray-100 text-gray-600' }}">
          {{ ucfirst($review->status) }}
        </span>

        <p class="mt-2 px-1 text-[11px] leading-snug text-gray-700">{{ Str::limit($review->description, 80) }} <span class="font-bold">see more.</span></p>
        <p class="mt-2 text-[11px] font-medium text-gray-800">More details</p>
      </div>
      <div class="mt-3 flex items-center justify-between px-1 text-[10px] text-gray-600">
        <span>👁 {{ number_format($review->views ?? 0) }} views</span>
        <span>📅 {{ $review->created_at->format('d M Y') }}</span>
      </div>
      @auth
        @if(auth()->user()->role === 'admin')
        <div class="mt-3 flex gap-2">
          @if($review->status !== 'accepted')
          <form method="POST" action="{{ route('admin.reviews.accept', $review->id) }}" class="flex-1">
            @csrf
            @method('PATCH')
            <button type="submit" class="w-full rounded-md bg-brand-600 py-1.5 text-[11px] font-bold tracking-wide text-white hover:bg-brand-700 transition">ACCEPT</button>
          </form>
          @endif
          @if($review->status !== 'declined')
          <form method="POST" action="{{ route('admin.reviews.decline', $review->id) }}" class="flex-1">
            @csrf
            @method('PATCH')
            <button type="submit" class="w-full rounded-md border border-brand-600 py-1.5 text-[11px] font-bold tracking-wide text-brand-600 hover:bg-brand-50 transition">DECLINE</button>
          </form>
          @endif
        </div>
        @endif
      @endauth
    </div>
    @empty
    <div class="col-span-full py-12 text-center text-gray-500 bg-gray-50 rounded-2xl border border-gray-200">
      Belum ada review yang tersedia.
    </div>
    @endforelse
  </div>

  @if($reviews->hasPages())
  <div class="mt-10">
    {{ $reviews->links() }}
  </div>
  @endif
</main>
</x-layouts.app>
