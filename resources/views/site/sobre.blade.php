{{--
    CultoGestor — Página: Sobre o projeto

    @author Eudes S. Aguiar — ProezaTech — www.proezatech.com
    @link   https://www.proezatech.com
--}}
@extends('layouts.site')

@section('title', 'Sobre')
@section('meta_description', 'O CultoGestor nasceu para substituir planilhas, grupos de WhatsApp e pastas soltas por uma central única de operação do culto. Conheça o projeto.')

@section('content')
    {{-- Cabeçalho --}}
    <section class="bg-navy-900">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold tracking-wide text-gold-400 uppercase">Sobre</p>
            <h1 class="mt-2 font-serif text-4xl font-bold tracking-tight text-white sm:text-5xl">Sobre o CultoGestor</h1>
            <p class="mt-4 max-w-2xl text-lg text-navy-100">
                Um sistema feito por quem vive a operação do culto, para tornar o trabalho da equipe técnica e de louvor mais leve.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="space-y-12">
            <div>
                <h2 class="font-serif text-2xl font-bold text-navy-900">Por que existe</h2>
                <p class="mt-4 leading-relaxed text-navy-700">
                    A operação técnica de um culto envolve muita gente e muitos detalhes: escalas, músicas dos cantores,
                    mídias, anúncios, projeção e transmissão. Quando isso vive espalhado em planilhas, grupos de WhatsApp e
                    pastas soltas no Drive, informação se perde e a equipe trabalha no improviso. O CultoGestor reúne tudo
                    em um só lugar.
                </p>
            </div>

            <div>
                <h2 class="font-serif text-2xl font-bold text-navy-900">Para quem é</h2>
                <p class="mt-4 leading-relaxed text-navy-700">
                    Para as equipes que fazem o culto acontecer nos bastidores — sonoplastia, projeção, transmissão,
                    fotografia, músicos e cantores especiais — e para a direção que coordena tudo isso.
                </p>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    @foreach ([
                        'Sonoplastia', 'Projeção', 'Transmissão', 'Músicos & cantores', 'Fotografia', 'Direção do culto',
                    ] as $publico)
                        <div class="flex items-center gap-3 rounded-xl border border-navy-100 bg-white px-4 py-3">
                            <span class="h-2 w-2 rounded-full bg-gold-500"></span>
                            <span class="text-sm font-medium text-navy-800">{{ $publico }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <h2 class="font-serif text-2xl font-bold text-navy-900">Projeto aberto a colaboradores</h2>
                <p class="mt-4 leading-relaxed text-navy-700">
                    O CultoGestor é desenvolvido por <strong>Eudes S. Aguiar</strong> na
                    <a href="https://www.proezatech.com" target="_blank" rel="noopener" class="font-medium text-navy-900 underline decoration-gold-500 underline-offset-4 hover:text-gold-600">ProezaTech</a>,
                    com o repositório aberto para receber colaboradores. Se você quer ajudar a construir, sua contribuição é bem-vinda.
                </p>
            </div>
        </div>

        <div class="mt-14 rounded-2xl bg-navy-900 px-8 py-10 text-center">
            <h2 class="font-serif text-2xl font-bold text-white">Quer conversar sobre o projeto?</h2>
            <a href="{{ route('site.contato') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-3 text-sm font-semibold text-navy-950 transition-colors hover:bg-gold-400">
                Entrar em contato
            </a>
        </div>
    </section>
@endsection
