@props(['status'])

@php
    $classes = 'px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide inline-flex items-center justify-center';
    $icon = '';

    switch(strtolower($status)) {
        case 'hadir':
            $classes .= ' bg-emerald-100 text-emerald-700 border border-emerald-200';
            $icon = 'fa-check';
            break;
        case 'sakit':
            $classes .= ' bg-rose-100 text-rose-700 border border-rose-200';
            $icon = 'fa-procedures';
            break;
        case 'izin':
            $classes .= ' bg-amber-100 text-amber-700 border border-amber-200';
            $icon = 'fa-file-alt';
            break;
        case 'cuti':
            $classes .= ' bg-purple-100 text-purple-700 border border-purple-200';
            $icon = 'fa-umbrella-beach';
            break;
        case 'lembur':
            $classes .= ' bg-indigo-100 text-indigo-700 border border-indigo-200';
            $icon = 'fa-briefcase';
            break;
        case 'alpa':
        case 'tidak hadir':
            $classes .= ' bg-gray-200 text-gray-600 border border-gray-300';
            $icon = 'fa-times';
            break;
        default:
            $classes .= ' bg-gray-100 text-gray-500';
            $icon = 'fa-minus';
    }
@endphp

<span class="{{ $classes }}">
    <i class="fas {{ $icon }} mr-1.5 text-[10px]"></i> {{ $status }}
</span>