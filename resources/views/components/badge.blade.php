@props(['color' => 'gray', 'size' => 'sm'])

@php
$colors = [
    'gray'   => 'bg-gray-100 text-gray-600',
    'blue'   => 'bg-blue-100 text-blue-700',
    'green'  => 'bg-green-100 text-green-700',
    'red'    => 'bg-red-100 text-red-600',
    'yellow' => 'bg-yellow-100 text-yellow-700',
    'purple' => 'bg-purple-100 text-purple-700',
    'brand'  => 'bg-brand-600/10 text-brand-700',
];
$sizes = ['xs' => 'px-2 py-0.5 text-[10px]', 'sm' => 'px-2.5 py-0.5 text-xs', 'md' => 'px-3 py-1 text-sm'];
$cls = ($colors[$color] ?? $colors['gray']) . ' ' . ($sizes[$size] ?? $sizes['sm']);
@endphp

<span {{ $attributes->merge(['class' => "inline-block rounded-full font-semibold {$cls}"]) }}>
    {{ $slot }}
</span>
