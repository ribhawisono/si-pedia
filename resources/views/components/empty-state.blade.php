@props(['icon' => null, 'title' => 'Tidak ada data', 'description' => null, 'action' => null, 'actionLabel' => 'Lihat Semua'])

<div class="flex flex-col items-center justify-center py-20 text-center empty-state" role="status">
    @if($icon)
    <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800" aria-hidden="true">
        {!! $icon !!}
    </div>
    @else
    <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800" aria-hidden="true">
        <svg class="h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
        </svg>
    </div>
    @endif
    <p class="text-xl font-bold text-gray-700 dark:text-gray-200">{{ $title }}</p>
    @if($description)
    <p class="mt-2 text-sm text-gray-400 max-w-sm">{{ $description }}</p>
    @endif
    @if($action)
    <a href="{{ $action }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition focus:outline-none focus:ring-2 focus:ring-brand-400">
        {{ $actionLabel }}
    </a>
    @endif
    {{ $slot }}
</div>
