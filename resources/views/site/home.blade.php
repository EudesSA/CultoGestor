{{--
    CultoGestor — Página inicial (Home)

    @author Eudes S. Aguiar — ProezaTech — www.proezatech.com
    @link   https://www.proezatech.com
--}}
@extends('layouts.site')

@section('title', 'CultoGestor')
@section('meta_description', 'Organize escalas, músicas, mídias, Louvor JA e o Modo Culto em tempo real. A central operacional do culto para equipes técnicas e de louvor.')

@section('content')
    <x-site.hero />

    {{-- Faixa de benefícios --}}
    <section class="border-b border-navy-100 bg-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:grid-cols-2 sm:px-6 lg:grid-cols-4 lg:px-8">
            @foreach ([
                ['Tudo em um só lugar', 'Sem planilhas perdidas ou pastas soltas no Drive.'],
                ['Equipe sincronizada', 'Escalas e confirmações por link, sem grupos lotados.'],
                ['Pronto para o Louvor JA', 'Exporte a liturgia direto para o software de projeção.'],
                ['Modo Culto em tempo real', 'Todas as telas avançando juntas durante o culto.'],
            ] as [$titulo, $texto])
                <div>
                    <p class="font-serif text-lg font-semibold text-navy-900">{{ $titulo }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-navy-700">{{ $texto }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Módulos --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <x-site.section-heading
            center
            eyebrow="Funcionalidades"
            title="Cada etapa do culto, coberta"
            subtitle="Do planejamento da escala até a projeção no telão, o CultoGestor acompanha todo o fluxo da operação técnica." />

        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <x-site.feature-card
                icon="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"
                title="Calendário & Escalas"
                text="Crie cultos, monte a escala por função e receba a confirmação de cada voluntário por link." />
            <x-site.feature-card
                icon="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"
                title="Portal do Cantor"
                text="Cada cantor acessa por link próprio, envia playback, letra e cifra, e confirma a participação." />
            <x-site.feature-card
                icon="M2.25 12.75V12a2.25 2.25 0 012.25-2.25h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"
                title="Google Drive & Calendar"
                text="Os arquivos sobem para uma estrutura organizada no Drive e os cultos refletem no Google Calendar." />
            <x-site.feature-card
                icon="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6"
                title="Exportação Louvor JA"
                text="Gere a liturgia no formato .ja com os caminhos certos e importe direto no Louvor JA." />
            <x-site.feature-card
                icon="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"
                title="Modo Culto"
                text="Tela cheia, player embutido e sincronização em tempo real entre sonoplastia, projeção e transmissão." />
            <x-site.feature-card
                icon="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25"
                title="Bibliotecas de mídia"
                text="Provai e Vede, Informativos e Anúncios organizados e prontos para entrar na liturgia do culto." />
        </div>
    </section>

    {{-- Resumo "como funciona" --}}
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <x-site.section-heading
                center
                eyebrow="Fluxo"
                title="Três passos, do planejamento ao telão" />
            <div class="mt-12 grid gap-8 md:grid-cols-3">
                @foreach ([
                    ['01', 'Planeje', 'Cadastre o culto, monte a liturgia e escale a equipe por função.'],
                    ['02', 'Prepare', 'Cantores enviam músicas, os arquivos sincronizam no Drive e gera-se o .ja.'],
                    ['03', 'Conduza', 'No dia, o Modo Culto sincroniza todas as telas em tempo real.'],
                ] as [$num, $titulo, $texto])
                    <div class="relative rounded-2xl border border-navy-100 bg-cream p-6">
                        <span class="font-serif text-3xl font-bold text-gold-500">{{ $num }}</span>
                        <h3 class="mt-2 font-serif text-lg font-semibold text-navy-900">{{ $titulo }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-navy-700">{{ $texto }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-10 text-center">
                <a href="{{ route('site.como-funciona') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-navy-800 hover:text-gold-600">
                    Ver o fluxo completo
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- CTA final --}}
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-3xl bg-navy-900 px-8 py-14 text-center shadow-xl">
            <h2 class="font-serif text-3xl font-bold text-white sm:text-4xl">Pronto para organizar seu culto?</h2>
            <p class="mx-auto mt-4 max-w-xl text-lg text-navy-100">
                Fale com a gente e veja o CultoGestor funcionando na sua igreja.
            </p>
            <a href="{{ route('site.contato') }}"
               class="mt-8 inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-3 text-sm font-semibold text-navy-950 shadow-lg transition-colors hover:bg-gold-400">
                Entrar em contato
            </a>
        </div>
    </section>
@endsection
