@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full px-3 py-2.5 text-sm text-zinc-800 placeholder-zinc-400 bg-white border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary/60 transition']) !!}>