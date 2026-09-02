@props([
    'headers' => null, // Array of header strings: ['No', 'Nama', 'Aksi']
    'striped' => false,
    'hoverable' => true,
    'compact' => true,
    'responsive' => true,
    'variant' => 'glass', // 'glass', 'solid'
])

@php
    $containerClass = $variant === 'solid' ? 'm3-card overflow-hidden' : 'm3-glass-card overflow-hidden';

    $tableClass = 'm3-table';
    if ($striped) {
        $tableClass .= ' [&_tbody_tr:nth-child(even)]:bg-zinc-50/40 dark:[&_tbody_tr:nth-child(even)]:bg-zinc-900/30';
    }
    if (!$hoverable) {
        $tableClass .= ' [&_tbody_tr:hover]:bg-transparent';
    }
    if ($compact) {
        $tableClass .= ' [&_th]:py-2.5 [&_th]:px-3.5 [&_td]:py-2.5 [&_td]:px-3.5';
    }
@endphp

<div class="{{ $containerClass }}">
    @if ($responsive)
        <div class="overflow-x-auto custom-scrollbar w-full">
    @endif

    <table {{ $attributes->merge(['class' => $tableClass]) }}>
        @if (isset($thead))
            <thead>
                {{ $thead }}
            </thead>
        @elseif (!empty($headers))
            <thead>
                <tr>
                    @foreach ($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif

        <tbody>
            {{ $slot }}
        </tbody>

        @if (isset($tfoot))
            <tfoot>
                {{ $tfoot }}
            </tfoot>
        @endif
    </table>

    @if ($responsive)
</div>
@endif
</div>
