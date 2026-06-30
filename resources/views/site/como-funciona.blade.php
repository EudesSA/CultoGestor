{{--
    CultoGestor — Página: Como funciona (fluxo passo a passo)

    @author Eudes S. Aguiar — ProezaTech — www.proezatech.com
    @link   https://www.proezatech.com
--}}
@extends('layouts.site')

@section('title', 'Como funciona')
@section('meta_description', 'O ciclo de um culto no CultoGestor, do planejamento da escala à sincronização das telas no Modo Culto — passo a passo.')

@section('content')
    {{-- Cabeçalho --}}
    <section class="bg-navy-900">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold tracking-wide text-gold-400 uppercase">Como funciona</p>
            <h1 class="mt-2 font-serif text-4xl font-bold tracking-tight text-white sm:text-5xl">O ciclo de um culto, do começo ao fim</h1>
            <p class="mt-4 max-w-2xl text-lg text-navy-100">
                Seis etapas que levam o planejamento até o telão — cada uma com responsáveis claros e nada perdido pelo caminho.
            </p>
        </div>
    </section>

    {{-- Timeline --}}
    <section class="mx-auto max-w-3xl px-4 py-20 sm:px-6 lg:px-8">
        <ol class="relative space-y-10 border-s-2 border-navy-100 ps-8">
            @foreach ([
                ['Crie o culto e a liturgia', 'Cadastre a data, o tema e monte a ordem do culto item a item: músicas, vídeos, anúncios e demais elementos.'],
                ['Escale a equipe', 'Atribua cada função técnica a um voluntário. Ele recebe um link e confirma (ou recusa) a participação.'],
                ['Cantores enviam as músicas', 'Pelo portal próprio, cada cantor envia playback, letra, cifra e tom. A direção aprova o que entra.'],
                ['Sincronize com o Drive', 'Os arquivos sobem automaticamente para uma estrutura organizada por data no Google Drive.'],
                ['Gere a liturgia Louvor JA', 'Com um clique, exporte o arquivo .ja na ordem certa e importe no Louvor JA para projeção.'],
                ['Conduza no Modo Culto', 'No dia, abra o Modo Culto em tela cheia. Ao avançar um item, todas as telas da equipe acompanham em tempo real.'],
            ] as $i => [$titulo, $texto])
                <li class="relative">
                    <span class="absolute -start-[2.6rem] flex h-8 w-8 items-center justify-center rounded-full bg-navy-900 font-serif text-sm font-bold text-gold-500 ring-4 ring-cream">{{ $i + 1 }}</span>
                    <h3 class="font-serif text-xl font-semibold text-navy-900">{{ $titulo }}</h3>
                    <p class="mt-2 leading-relaxed text-navy-700">{{ $texto }}</p>
                </li>
            @endforeach
        </ol>

        <div class="mt-14 rounded-2xl border border-navy-100 bg-white p-8 text-center">
            <h2 class="font-serif text-2xl font-bold text-navy-900">Veja cada módulo em detalhe</h2>
            <p class="mt-2 text-navy-700">Entenda tudo o que o CultoGestor faz em cada etapa do fluxo.</p>
            <a href="{{ route('site.funcionalidades') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-3 text-sm font-semibold text-navy-950 transition-colors hover:bg-gold-400">
                Ver as funcionalidades
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    </section>
@endsection
