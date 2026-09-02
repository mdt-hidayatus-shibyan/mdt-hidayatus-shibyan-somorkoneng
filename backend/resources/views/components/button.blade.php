@props([
    'variant' => 'primary', // 'primary', 'secondary', 'danger', 'outline', 'ghost', 'text'
    'size' => 'md', // 'sm', 'md', 'lg'
    'icon' => null,
    'iconPosition' => 'left',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
])

@php
    $baseStyles =
        'inline-flex items-center justify-center font-bold tracking-tight transition-all duration-200 focus:outline-none select-none disabled:opacity-50 disabled:pointer-events-none';

    $sizeStyles = match ($size) {
        'sm' => 'min-h-[36px] px-3 py-1.5 text-xs rounded-xl gap-1.5',
        'lg' => 'min-h-[48px] px-6 py-3 text-sm md:text-base rounded-2xl md:rounded-3xl gap-2.5',
        default => 'min-h-[40px] md:min-h-[44px] px-4 py-2 text-[13px] rounded-xl md:rounded-2xl gap-2',
    };

    $variantStyles = match ($variant) {
        'secondary'
            => 'bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 active:scale-95 focus:ring-2 focus:ring-zinc-400/30',
        'danger'
            => 'bg-red-600 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-500 text-white shadow-sm hover:shadow active:scale-95 focus:ring-2 focus:ring-red-500/30',
        'outline'
            => 'bg-transparent hover:bg-primary/10 dark:hover:bg-primary-dark/10 text-primary dark:text-primary-dark border border-primary/30 dark:border-primary-dark/30 active:scale-95 focus:ring-2 focus:ring-primary/20',
        'ghost'
            => 'bg-transparent hover:bg-zinc-100 dark:hover:bg-zinc-800/80 text-zinc-700 dark:text-zinc-300 active:scale-95',
        'text' => 'bg-transparent text-primary dark:text-primary-dark hover:underline p-0 min-h-0',
        default
            => 'bg-primary dark:bg-primary-dark text-white dark:text-zinc-950 shadow-sm hover:shadow-md hover:bg-primary-700 dark:hover:bg-primary-400 active:scale-95 focus:ring-2 focus:ring-primary/30 dark:focus:ring-primary-dark/30',
    };

    $classes = "{$baseStyles} {$sizeStyles} {$variantStyles}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon && $iconPosition === 'left')
            <i class="{{ $icon }} text-base leading-none"></i>
        @endif

        @if ($slot->isNotEmpty())
            <span>{{ $slot }}</span>
        @endif

        @if ($icon && $iconPosition === 'right')
            <i class="{{ $icon }} text-base leading-none"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon && $iconPosition === 'left')
            <i class="{{ $icon }} text-base leading-none"></i>
        @endif

        @if ($slot->isNotEmpty())
            <span>{{ $slot }}</span>
        @endif

        @if ($icon && $iconPosition === 'right')
            <i class="{{ $icon }} text-base leading-none"></i>
        @endif
    </button>
@endif
