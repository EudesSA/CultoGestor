{{--
    CultoGestor — Componente: título de seção
    Props: eyebrow (opcional), title, subtitle (opcional), center (bool)

    @author Eudes S. Aguiar — ProezaTech — www.proezatech.com
    @link   https://www.proezatech.com
--}}
@props([
    'eyebrow' => null,
    'title' => '',
    'subtitle' => null,
    'center' => false,
])

<div @class(['max-w-2xl', 'mx-auto text-center' => $center])>
    @if ($eyebrow)
        <p class="text-sm font-semibold tracking-wide text-gold-600 uppercase">{{ $eyebrow }}</p>
    @endif
    <h2 class="mt-2 font-serif text-3xl font-bold tracking-tight text-navy-900 sm:text-4xl">{{ $title }}</h2>
    @if ($subtitle)
        <p class="mt-4 text-lg leading-relaxed text-navy-700">{{ $subtitle }}</p>
    @endif
</div>
