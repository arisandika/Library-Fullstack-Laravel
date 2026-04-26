@props(['disabled' => false])

<button {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['type' => 'submit', 'class' => 'flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors active:scale-95 disabled:opacity-75 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>