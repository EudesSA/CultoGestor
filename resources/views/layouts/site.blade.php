{{--
    CultoGestor — Site público institucional
    Layout-mestre compartilhado por todas as páginas públicas (navbar + footer + SEO).
    Reutilizado via @extends('layouts.site').

    @author Eudes S. Aguiar — ProezaTech — www.proezatech.com
    @link   https://www.proezatech.com
--}}
<!DOCTYPE html>
<html lang="pt-BR" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO por página --}}
    <title>@yield('title', 'CultoGestor') — Central operacional do culto</title>
    <meta name="description" content="@yield('meta_description', 'CultoGestor: organize escalas, músicas, mídias e o Modo Culto da sua equipe técnica em um só lugar.')">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'CultoGestor')">
    <meta property="og:description" content="@yield('meta_description', 'Central operacional do culto para equipes técnicas e de louvor.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="pt_BR">

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="theme-color" content="#13294B">

    @vite(['resources/css/site.css', 'resources/js/site.js'])
    @stack('head')
</head>
<body class="flex min-h-full flex-col bg-cream font-sans text-navy-950 antialiased">

    <x-site.navbar />

    <main class="flex-1">
        @yield('content')
    </main>

    <x-site.footer />

    @stack('scripts')
</body>
</html>
