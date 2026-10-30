@props([
    'size' => 'md',
    'showTagline' => true,
    'dark' => false,
])

@php
    $imgClasses = $size === 'sm'
        ? 'h-8 w-8'
        : ($size === 'lg' ? 'h-11 w-11' : 'h-9 w-9 sm:h-10 sm:w-10');
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
    @if ($dark)
        <span class="tu-logo-mark inline-grid h-10 w-10 shrink-0 place-items-center rounded bg-white p-1.5">
            <img src="{{ asset('images/logo-telyuji.png') }}" alt="" width="32" height="32" class="h-full w-full object-contain">
        </span>
    @else
        <img src="{{ asset('images/logo-telyuji.png') }}" alt="" width="40" height="40" class="tu-logo-mark {{ $imgClasses }} shrink-0 object-contain">
    @endif

    <span class="flex flex-col leading-none">
        <span class="font-bold text-[17px] tracking-tight {{ $dark ? 'text-white' : 'text-gray-950' }}">Telyuji</span>
        @if ($showTagline)
            <span class="text-[11px] font-medium {{ $dark ? 'text-gray-400' : 'text-gray-500' }} mt-1">Teliti dan Teruji</span>
        @endif
    </span>
</span>
