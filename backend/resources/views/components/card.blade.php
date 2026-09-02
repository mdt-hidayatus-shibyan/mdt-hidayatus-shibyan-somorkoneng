@props([
    'variant' => 'glass', // 'glass', 'solid', 'flat', 'outlined'
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'compact' => false,
    'padding' => null,
])

@php
    $baseClass = match ($variant) {
        'solid' => 'm3-card',
        'flat'
            => 'bg-zinc-50/70 dark:bg-zinc-900/40 rounded-2xl md:rounded-3xl border border-zinc-200/60 dark:border-zinc-800/60 transition-all duration-300',
        'outlined'
            => 'bg-transparent rounded-2xl md:rounded-3xl border border-zinc-300 dark:border-zinc-700 transition-all duration-300',
        default => 'm3-glass-card',
    };

    $padClass = $padding ?? ($compact ? 'p-3 md:p-4' : 'p-4 md:p-6');
@endphp

<div {{ $attributes->merge(['class' => "{$baseClass} {$padClass} relative flex flex-col"]) }}>
    @if ($title || isset($header) || isset($headerAction))
        <div
            class="flex items-center justify-between gap-3 mb-4 pb-3 border-b border-zinc-100 dark:border-zinc-800/80 shrink-0">
            @if (isset($header))
                {{ $header }}
            @else
                <div class="flex items-center gap-2.5 min-w-0">
                    @if ($icon)
                        <div
                            class="w-9 h-9 rounded-xl bg-primary/10 dark:bg-primary-dark/10 text-primary dark:text-primary-dark flex items-center justify-center shrink-0">
                            <i class="{{ $icon }} text-base"></i>
                        </div>
                    @endif
                    <div class="min-w-0">
                        @if ($title)
                            <h3
                                class="text-base md:text-lg font-bold text-zinc-900 dark:text-white tracking-tight truncate">
                                {{ $title }}
                            </h3>
                        @endif
                        @if ($subtitle)
                            <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5 truncate">
                                {{ $subtitle }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            @if (isset($headerAction))
                <div class="flex items-center gap-2 shrink-0">
                    {{ $headerAction }}
                </div>
            @endif
        </div>
    @endif

    <div class="flex-1 w-full">
        {{ $slot }}
    </div>

    @if (isset($footer))
        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 shrink-0">
            {{ $footer }}
        </div>
    @endif
</div>
