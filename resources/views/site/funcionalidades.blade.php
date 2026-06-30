{{--
    CultoGestor — Página: Funcionalidades (detalhamento dos módulos)

    @author Eudes S. Aguiar — ProezaTech — www.proezatech.com
    @link   https://www.proezatech.com
--}}
@extends('layouts.site')

@section('title', 'Funcionalidades')
@section('meta_description', 'Conheça em detalhe os módulos do CultoGestor: escalas, portal do cantor, Google Drive, bibliotecas de mídia, exportação Louvor JA e Modo Culto.')

@section('content')
    {{-- Cabeçalho --}}
    <section class="bg-navy-900">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold tracking-wide text-gold-400 uppercase">Funcionalidades</p>
            <h1 class="mt-2 font-serif text-4xl font-bold tracking-tight text-white sm:text-5xl">Tudo o que a operação do culto precisa</h1>
            <p class="mt-4 max-w-2xl text-lg text-navy-100">
                Cada módulo resolve uma dor real da equipe técnica e de louvor. Veja como eles se encaixam no dia a dia.
            </p>
        </div>
    </section>

    {{-- Seções alternadas por módulo --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @php
            $modulos = [
                [
                    'eyebrow' => 'Programação',
                    'titulo' => 'Calendário & Escalas',
                    'texto' => 'Crie cultos por tipo, monte a liturgia item a item e escale cada função técnica. Os voluntários confirmam por um link tokenizado — sem precisar de conta.',
                    'itens' => ['Escala por função (sonoplastia, projeção, transmissão, músicos)', 'Confirmação por link, com registro de quem confirmou', 'Visão de calendário dos próximos cultos'],
                ],
                [
                    'eyebrow' => 'Músicas & Cantores',
                    'titulo' => 'Portal do Cantor',
                    'texto' => 'Cada cantor recebe um link próprio e envia as músicas que vai cantar, com playback, letra, cifra e tom. O histórico de participação é registrado automaticamente.',
                    'itens' => ['Acesso por link, sem login no painel', 'Upload de MP3, playback, letra, cifra e partitura', 'Aprovação das músicas pela direção'],
                ],
                [
                    'eyebrow' => 'Integrações',
                    'titulo' => 'Google Drive & Calendar',
                    'texto' => 'Os arquivos enviados sobem para uma estrutura organizada por data no Google Drive, e os cultos podem refletir automaticamente no Google Calendar da equipe.',
                    'itens' => ['Pastas organizadas por ano/mês/data e categoria', 'Sincronização dos cultos com o Google Calendar', 'Sem upload manual nem pastas duplicadas'],
                ],
                [
                    'eyebrow' => 'Bibliotecas',
                    'titulo' => 'Provai e Vede, Informativos & Anúncios',
                    'texto' => 'Mantenha bibliotecas de vídeos e mídias prontas para entrar na liturgia: o quadro Provai e Vede, informativos da igreja e anúncios com período de exibição.',
                    'itens' => ['Vídeos e mídias categorizados', 'Anúncios com data de início e fim', 'Seleção direta na liturgia do culto'],
                ],
                [
                    'eyebrow' => 'Projeção',
                    'titulo' => 'Exportação Louvor JA',
                    'texto' => 'Gere a liturgia no formato .ja com os itens na ordem certa e os caminhos locais configuráveis, pronta para importar no Louvor JA sem retrabalho.',
                    'itens' => ['Geração do arquivo .ja a partir da liturgia', 'Caminho-raiz local configurável (sem hardcode)', 'Download seguro por link dedicado'],
                ],
                [
                    'eyebrow' => 'Ao vivo',
                    'titulo' => 'Modo Culto',
                    'texto' => 'Durante o culto, uma tela cheia conduz a operação com player embutido, atalhos e sincronização em tempo real entre todos os operadores.',
                    'itens' => ['Sincronização em tempo real (multi-tela)', 'Player de mídia embutido', 'Atalhos para avançar a liturgia'],
                ],
            ];
        @endphp

        @foreach ($modulos as $i => $m)
            <section class="grid items-center gap-10 border-b border-navy-100 py-16 lg:grid-cols-2 lg:gap-16">
                <div @class(['lg:order-2' => $i % 2 === 1])>
                    <p class="text-sm font-semibold tracking-wide text-gold-600 uppercase">{{ $m['eyebrow'] }}</p>
                    <h2 class="mt-2 font-serif text-3xl font-bold text-navy-900">{{ $m['titulo'] }}</h2>
                    <p class="mt-4 text-lg leading-relaxed text-navy-700">{{ $m['texto'] }}</p>
                    <ul class="mt-6 space-y-3">
                        @foreach ($m['itens'] as $item)
                            <li class="flex items-start gap-3">
                                <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-gold-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                <span class="text-navy-800">{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div @class(['lg:order-1' => $i % 2 === 1])>
                    <div class="flex aspect-[4/3] items-center justify-center rounded-2xl bg-gradient-to-br from-navy-900 to-navy-700 shadow-xl">
                        <svg class="h-20 w-20 text-gold-500/80" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z"/>
                        </svg>
                    </div>
                </div>
            </section>
        @endforeach
    </div>

    {{-- CTA --}}
    <section class="mx-auto max-w-7xl px-4 py-16 text-center sm:px-6 lg:px-8">
        <h2 class="font-serif text-3xl font-bold text-navy-900">Quer ver na prática?</h2>
        <a href="{{ route('site.contato') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-navy-900 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-navy-800">
            Fale com a gente
        </a>
    </section>
@endsection
