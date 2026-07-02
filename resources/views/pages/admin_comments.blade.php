<x-layouts.admin title="Moderasi Komentar — SI-Pedia" section="comments">

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
  <div>
    <h2 class="page-title">Moderasi Komentar</h2>
    <p class="page-subtitle">Tinjau dan kelola komentar dari pengguna.</p>
  </div>
  {{-- Bulk action form --}}
  <div id="bulk-bar" class="hidden items-center gap-2">
    <span class="text-sm font-semibold text-gray-700"><span id="bulk-count">0</span> dipilih</span>
    <form id="bulk-form" method="POST" action="{{ route('admin.comments.bulk') }}" class="flex gap-2">
      @csrf
      <div id="bulk-ids"></div>
      <button type="submit" name="action" value="approve" class="rounded-lg bg-green-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-green-600 transition">✅ Approve</button>
      <button type="submit" name="action" value="reject"  class="rounded-lg bg-orange-400 px-3 py-1.5 text-xs font-bold text-white hover:bg-orange-500 transition">❌ Tolak</button>
      <button type="submit" name="action" value="delete"  class="rounded-lg bg-red-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-red-600 transition"
              onclick="return confirm('Hapus komentar terpilih?')">🗑 Hapus</button>
    </form>
  </div>
</div>

{{-- Stats --}}
<div class="mb-6 grid grid-cols-3 gap-4">
  <a href="{{ route('admin.comments.index') }}" class="rounded-xl border bg-white p-4 text-center shadow-sm hover:shadow-md transition {{ !request('status') ? 'border-brand-300 ring-1 ring-brand-300' : 'border-gray-200' }}">
    <p class="text-2xl font-black text-gray-900">{{ $counts['pending'] + $counts['approved'] + $counts['rejected'] }}</p>
    <p class="text-xs font-semibold text-gray-500 mt-1">Total</p>
  </a>
  <a href="{{ route('admin.comments.index', ['status'=>'pending']) }}" class="rounded-xl border bg-white p-4 text-center shadow-sm hover:shadow-md transition {{ request('status')==='pending' ? 'border-yellow-400 ring-1 ring-yellow-400' : 'border-gray-200' }}">
    <p class="text-2xl font-black text-yellow-500">{{ $counts['pending'] }}</p>
    <p class="text-xs font-semibold text-gray-500 mt-1">Pending</p>
  </a>
  <a href="{{ route('admin.comments.index', ['status'=>'approved']) }}" class="rounded-xl border bg-white p-4 text-center shadow-sm hover:shadow-md transition {{ request('status')==='approved' ? 'border-green-400 ring-1 ring-green-400' : 'border-gray-200' }}">
    <p class="text-2xl font-black text-green-600">{{ $counts['approved'] }}</p>
    <p class="text-xs font-semibold text-gray-500 mt-1">Approved</p>
  </a>
</div>

{{-- Comments list --}}
<div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
  <table class="w-full text-left text-sm">
    <thead>
      <tr class="border-b border-gray-100 bg-gray-50 text-xs font-bold uppercase tracking-wide text-gray-500">
        <th class="py-3 px-4 w-8">
          <input type="checkbox" id="select-all" aria-label="Pilih semua komentar"
                 class="rounded border-gray-300 text-brand-600 focus:ring-brand-600">
        </th>
        <th class="py-3 px-4">Komentar</th>
        <th class="py-3 px-4 hidden sm:table-cell">Artikel</th>
        <th class="py-3 px-4 hidden md:table-cell">Waktu</th>
        <th class="py-3 px-4">Status</th>
        <th class="py-3 px-4">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
      @forelse($comments as $comment)
      <tr class="hover:bg-gray-50 transition-colors" data-id="{{ $comment->id }}">
        <td class="py-4 px-4">
          <input type="checkbox" name="comment_ids[]" value="{{ $comment->id }}"
                 class="comment-checkbox rounded border-gray-300 text-brand-600 focus:ring-brand-600"
                 aria-label="Pilih komentar dari {{ $comment->user->name ?? 'Anonim' }}">
        </td>
        <td class="py-4 px-4">
          <div class="flex items-start gap-3">
            <img src="{{ $comment->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name ?? 'A') . '&background=336cbc&color=fff&size=80' }}"
                 alt="Foto {{ $comment->user->name }}" class="h-8 w-8 flex-shrink-0 rounded-full object-cover">
            <div class="min-w-0">
              <p class="text-xs font-bold text-gray-900">{{ $comment->user->name ?? 'Anonim' }}</p>
              <p class="mt-0.5 text-sm text-gray-700 line-clamp-2">{{ $comment->content }}</p>
            </div>
          </div>
        </td>
        <td class="py-4 px-4 hidden sm:table-cell">
          @if($comment->article)
          <a href="{{ route('articles.show', $comment->article->slug) }}" target="_blank"
             class="text-xs font-semibold text-brand-600 hover:text-brand-700 line-clamp-2 max-w-[160px] block">
            {{ $comment->article->title }}
          </a>
          @else
          <span class="text-xs text-gray-400">Artikel dihapus</span>
          @endif
        </td>
        <td class="py-4 px-4 hidden md:table-cell">
          <time class="text-xs text-gray-400" datetime="{{ $comment->created_at->toISOString() }}">
            {{ $comment->created_at->diffForHumans() }}
          </time>
        </td>
        <td class="py-4 px-4">
          <span class="rounded-full px-2.5 py-1 text-[10px] font-bold
            {{ $comment->status === 'approved' ? 'bg-green-100 text-green-700' :
               ($comment->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700') }}">
            {{ ucfirst($comment->status) }}
          </span>
        </td>
        <td class="py-4 px-4">
          <div class="flex items-center gap-1.5">
            @if($comment->status !== 'approved')
            <form action="{{ route('admin.comments.approve', $comment) }}" method="POST">
              @csrf @method('PATCH')
              <button type="submit" class="rounded-lg bg-green-500 px-2.5 py-1 text-[10px] font-bold text-white hover:bg-green-600 transition" aria-label="Setujui komentar">✅</button>
            </form>
            @endif
            @if($comment->status !== 'rejected')
            <form action="{{ route('admin.comments.reject', $comment) }}" method="POST">
              @csrf @method('PATCH')
              <button type="submit" class="rounded-lg bg-orange-400 px-2.5 py-1 text-[10px] font-bold text-white hover:bg-orange-500 transition" aria-label="Tolak komentar">❌</button>
            </form>
            @endif
            <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST"
                  onsubmit="return confirm('Hapus komentar ini?')">
              @csrf @method('DELETE')
              <button type="submit" class="rounded-lg bg-red-500 px-2.5 py-1 text-[10px] font-bold text-white hover:bg-red-600 transition" aria-label="Hapus komentar">🗑</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" class="py-12 text-center text-sm text-gray-400">Tidak ada komentar.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-5">{{ $comments->links() }}</div>

<script>
// Bulk checkbox logic
(function(){
    const selectAll  = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.comment-checkbox');
    const bar        = document.getElementById('bulk-bar');
    const cnt        = document.getElementById('bulk-count');
    const idsEl      = document.getElementById('bulk-ids');

    function update() {
        const checked = [...checkboxes].filter(c => c.checked);
        const n = checked.length;
        cnt.textContent = n;
        bar.classList.toggle('hidden', n === 0);
        bar.classList.toggle('flex', n > 0);
        idsEl.innerHTML = checked.map(c => `<input type="hidden" name="ids[]" value="${c.value}">`).join('');
    }

    selectAll?.addEventListener('change', () => {
        checkboxes.forEach(c => c.checked = selectAll.checked);
        update();
    });
    checkboxes.forEach(c => c.addEventListener('change', update));
})();
</script>

</x-layouts.admin>
