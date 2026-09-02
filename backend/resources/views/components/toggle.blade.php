@props([
    'name' => null,
    'id' => null,
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'label' => null,
    'activeText' => 'Aktif',
    'inactiveText' => 'Nonaktif',
    'url' => null,
    'ajax' => false,
])

@php
    $inputId = $id ?? ($name ? $name . '_' . uniqid() : 'toggle_' . uniqid());
    $isAjax = $ajax || !empty($url);
@endphp

<div class="inline-flex items-center gap-2.5">
    <label for="{{ $inputId }}"
        class="relative inline-flex items-center cursor-pointer min-h-[40px] group select-none {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
        <input type="checkbox" id="{{ $inputId }}"
            @if ($name) name="{{ $name }}" @endif value="{{ $value }}"
            class="sr-only peer {{ $isAjax ? 'toggle-status-ajax' : '' }}"
            @if ($url) data-url="{{ $url }}" @endif
            @if ($name) data-name="{{ $name }}" @endif
            data-text-active="{{ $activeText }}" data-text-inactive="{{ $inactiveText }}"
            {{ $checked ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->whereStartsWith(['wire:', 'x-', '@']) }}>

        <!-- Track & Thumb (M3 Expressive OLED Optimized) -->
        <div
            class="relative w-11 h-6 bg-zinc-200 dark:bg-zinc-800 rounded-full peer peer-focus:ring-2 peer-focus:ring-primary/20 dark:peer-focus:ring-primary-dark/20 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white dark:after:bg-zinc-300 after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary dark:peer-checked:bg-primary-dark peer-checked:after:bg-white dark:peer-checked:after:bg-zinc-950 transition-colors duration-300">
        </div>

        <!-- Label / Status Text -->
        @if ($label)
            <span
                class="ml-2.5 text-[13px] font-bold text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">
                {{ $label }}
            </span>
        @elseif ($activeText || $inactiveText)
            <span
                class="status-text ml-2.5 text-[11px] font-bold uppercase tracking-wider transition-colors duration-200 text-zinc-500 dark:text-zinc-500 peer-checked:text-primary dark:peer-checked:text-primary-dark">
                {{ $checked ? $activeText : $inactiveText }}
            </span>
        @endif
    </label>
</div>
