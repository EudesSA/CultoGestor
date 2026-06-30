{{--
    CultoGestor — Componente Navbar do site público
    Navegação responsiva com menu mobile colapsável (Alpine).

    @author Eudes S. Aguiar — ProezaTech — www.proezatech.com
    @link   https://www.proezatech.com
--}}
@php
    $links = [
        ['rota' => 'site.home', 'label' => 'Início'],
        ['rota' => 'site.funcionalidades', 'label' => 'Funcionalidades'],
        ['rota' => 'site.como-funciona', 'label' => 'Como funciona'],
        ['rota' => 'site.sobre', 'label' => 'Sobre'],
        ['rota' => 'site.faq', 'label' => 'FAQ'],
        ['rota' => 'site.contato', 'label' => 'Contato'],
    ];
@endphp

<header
    x-data="{ open: false, scrolled: false }"
    @scroll.window="scrolled = window.scrollY > 8"
    class="sticky top-0 z-40 transition-shadow"
    :class="scrolled ? 'bg-white/95 shadow-md backdrop-blur' : 'bg-white'"
>
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        {{-- Logo --}}
        <a href="{{ route('site.home') }}" class="flex items-center gap-2.5">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-navy-900 text-gold-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z"/>
                </svg>
            </span>
            <span class="font-serif text-lg font-bold tracking-tight text-navy-900">CultoGestor</span>
        </a>

        {{-- Links + acesso ao sistema (desktop) --}}
        <div class="hidden items-center gap-2 lg:flex">
            <div class="flex items-center gap-1">
                @foreach ($links as $link)
                    <a href="{{ route($link['rota']) }}"
                       @class([
                           'rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                           'text-gold-600' => request()->routeIs($link['rota']),
                           'text-navy-700 hover:bg-navy-50 hover:text-navy-900' => ! request()->routeIs($link['rota']),
                       ])>
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            {{-- Botão de acesso ao sistema --}}
            <a href="{{ url('/admin/login') }}"
               class="ms-1 inline-flex items-center gap-2 rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-navy-800">
                <svg class="h-4 w-4 text-gold-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                </svg>
                Acesso ao sistema
            </a>
        </div>

        {{-- Botão menu mobile --}}
        <button type="button" @click="open = !open"
                class="inline-flex items-center justify-center rounded-lg p-2 text-navy-800 hover:bg-navy-50 lg:hidden"
                :aria-expanded="open" aria-label="Abrir menu">
            <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
            <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </nav>

    {{-- Menu mobile --}}
    <div x-show="open" x-cloak x-transition.opacity class="border-t border-navy-100 bg-white lg:hidden">
        <div class="space-y-1 px-4 py-3">
            @foreach ($links as $link)
                <a href="{{ route($link['rota']) }}"
                   @class([
                       'block rounded-lg px-3 py-2 text-base font-medium',
                       'bg-navy-50 text-gold-600' => request()->routeIs($link['rota']),
                       'text-navy-700 hover:bg-navy-50' => ! request()->routeIs($link['rota']),
                   ])>
                    {{ $link['label'] }}
                </a>
            @endforeach

            {{-- Botão de acesso ao sistema (mobile) --}}
            <a href="{{ url('/admin/login') }}"
               class="mt-2 flex items-center justify-center gap-2 rounded-lg bg-navy-900 px-4 py-2.5 text-base font-semibold text-white">
                <svg class="h-5 w-5 text-gold-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                </svg>
                Acesso ao sistema
            </a>
        </div>
    </div>
</header>
