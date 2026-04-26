@props(['active' => false])

@php
    $baseClasses = 'flex items-center gap-3 px-4 py-3 font-semibold text-sm rounded-lg transition-all duration-300';

    $activeClasses = 'bg-orange-50 text-[#d97757] border-l-2 border-[#d97757]';

    $inactiveClasses = 'text-zinc-600 hover:bg-zinc-50 border-l-2 border-transparent';

    $classes = $baseClasses . ' ' . ($active ? $activeClasses : $inactiveClasses);
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>