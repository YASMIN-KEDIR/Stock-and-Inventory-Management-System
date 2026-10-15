@props([
    'size' => 'md', // sm, md, lg, xl
    'showText' => true,
    'variant' => 'light', // light (white text on dark background) or dark (dark text on light background)
])

@php
    $sizeClasses = [
        'sm' => ['box' => 'w-8 h-8 rounded-xl text-base', 'title' => 'text-base', 'sub' => 'text-[9px]'],
        'md' => ['box' => 'w-10 h-10 rounded-2xl text-xl', 'title' => 'text-lg', 'sub' => 'text-[10px]'],
        'lg' => ['box' => 'w-14 h-14 rounded-2xl text-3xl', 'title' => 'text-2xl', 'sub' => 'text-xs'],
        'xl' => ['box' => 'w-20 h-20 rounded-3xl text-4xl', 'title' => 'text-3xl', 'sub' => 'text-sm'],
    ][$size] ?? ['box' => 'w-10 h-10 rounded-2xl text-xl', 'title' => 'text-lg', 'sub' => 'text-[10px]'];

    $titleColor = $variant === 'light' ? 'text-white' : 'text-slate-900';
    $subColor = $variant === 'light' ? 'text-emerald-400' : 'text-emerald-700';
@endphp

<div class="flex items-center gap-3">
    <!-- Normal Bold "B" -->
    <div class="shrink-0 {{ $sizeClasses['box'] }} bg-emerald-600 text-white font-black flex items-center justify-center shadow-md shadow-emerald-600/20 select-none">
        B
    </div>

    @if($showText)
        <div class="leading-tight">
            <span class="{{ $sizeClasses['title'] }} font-black tracking-tight {{ $titleColor }} block">
                Bedewi <span class="text-emerald-400">Sales</span>
            </span>
            <span class="{{ $sizeClasses['sub'] }} font-bold tracking-wider {{ $subColor }} uppercase block">
                Stock &bull; Inventory
            </span>
        </div>
    @endif
</div>
