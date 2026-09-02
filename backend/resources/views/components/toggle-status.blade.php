@props([
    'isActive' => false, // Default value jika tidak diisi
    'url' => '#',
    'textActive' => 'Aktif',
    'textInactive' => 'Tidak Aktif',
])

<div class="flex flex-col items-start sm:items-end gap-1">
    <!-- Area sentuh Dense M3: min-h-[40px] -->
    <label class="relative inline-flex items-center cursor-pointer min-h-[40px] group">
        <input type="checkbox" class="sr-only peer toggle-status-ajax" data-url="{{ $url }}" data-name="is_active"
            data-text-active="{{ $textActive }}" data-text-inactive="{{ $textInactive }}"
            {{ $isActive ? 'checked' : '' }}>

        <!-- Switch Track & Thumb (M3 OLED Optimized) -->
        <div
            class="relative w-11 h-6 bg-zinc-200 dark:bg-zinc-800 rounded-2xl peer peer-focus:ring-2 peer-focus:ring-primary/20 dark:peer-focus:ring-primary-dark/20 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white dark:after:bg-zinc-400 after:border-zinc-300 dark:after:border-transparent after:border after:rounded-2xl after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:bg-primary dark:peer-checked:bg-primary-dark peer-checked:after:bg-white dark:peer-checked:after:bg-zinc-900 peer-checked:after:border-transparent transition-colors duration-300">
        </div>

        <!-- Teks Status -->
        <!-- Memanfaatkan peer-checked untuk mengubah warna secara CSS murni ketika toggle diklik tanpa perlu edit JS -->
        <span
            class="status-text ml-3 text-[11px] font-bold uppercase tracking-widest transition-colors duration-300 text-zinc-500 dark:text-zinc-500 peer-checked:text-primary dark:peer-checked:text-primary-dark">
            {{ $isActive ? $textActive : $textInactive }}
        </span>
    </label>
</div>
