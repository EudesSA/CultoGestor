{{--
    CultoGestor — Componente: card de funcionalidade/módulo
    Props: icon (caminho SVG <path d="...">), title, text

    @author Eudes S. Aguiar — ProezaTech — www.proezatech.com
    @link   https://www.proezatech.com
--}}
@props([
    'icon' => 'M5 13l4 4L19 7',
    'title' => '',
    'text' => '',
])

<div class="group rounded-2xl border border-navy-100 bg-white p-6 shadow-sm transition-all hover:-translate-y-1 hover:border-navy-200 hover:shadow-lg">
    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-navy-900 text-gold-500 transition-colors group-hover:bg-navy-800">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
        </svg>
    </span>
    <h3 class="mt-4 font-serif text-lg font-semibold text-navy-900">{{ $title }}</h3>
    <p class="mt-2 text-sm leading-relaxed text-navy-700">{{ $text }}</p>
    {{ $slot }}
</div>
