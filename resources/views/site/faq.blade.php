{{--
    CultoGestor — Página: Perguntas frequentes (FAQ com accordion Alpine)

    @author Eudes S. Aguiar — ProezaTech — www.proezatech.com
    @link   https://www.proezatech.com
--}}
@extends('layouts.site')

@section('title', 'Perguntas frequentes')
@section('meta_description', 'Tire suas dúvidas sobre o CultoGestor: instalação, envio de músicas pelos cantores, integração com o Louvor JA, Google Drive e acesso da equipe.')

@section('content')
    {{-- Cabeçalho --}}
    <section class="bg-navy-900">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold tracking-wide text-gold-400 uppercase">FAQ</p>
            <h1 class="mt-2 font-serif text-4xl font-bold tracking-tight text-white sm:text-5xl">Perguntas frequentes</h1>
            <p class="mt-4 max-w-2xl text-lg text-navy-100">As dúvidas mais comuns de quem está conhecendo o CultoGestor.</p>
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="space-y-3" x-data="{ aberta: 0 }">
            @php
                $perguntas = [
                    ['Preciso instalar algum programa?', 'Não. O CultoGestor roda no navegador. A equipe acessa por um endereço web; os cantores recebem um link próprio que pode até ser "instalado" como app no celular.'],
                    ['Como os cantores enviam as músicas?', 'Cada cantor recebe um link pessoal (sem precisar de senha do painel) e, por ele, envia playback, letra, cifra e o tom de cada música. A direção aprova o que entra na liturgia.'],
                    ['Funciona com o Louvor JA?', 'Sim. O sistema gera a liturgia no formato .ja, com os itens na ordem certa e os caminhos locais configuráveis, pronta para importar no Louvor JA.'],
                    ['Os arquivos ficam no Google Drive?', 'Sim. As mídias enviadas sobem automaticamente para uma estrutura organizada por data no Google Drive da equipe, sem upload manual.'],
                    ['Várias pessoas podem usar ao mesmo tempo?', 'Sim. O Modo Culto sincroniza em tempo real: ao avançar um item em uma tela, sonoplastia, projeção e transmissão acompanham juntas.'],
                    ['Como a equipe acessa o sistema?', 'A equipe técnica entra pelo painel administrativo, com login próprio. O acesso fica disponível de forma discreta no rodapé do site, em "Acesso da equipe".'],
                ];
            @endphp

            @foreach ($perguntas as $i => [$pergunta, $resposta])
                <div class="overflow-hidden rounded-xl border border-navy-100 bg-white">
                    <button type="button" @click="aberta = (aberta === {{ $i }} ? null : {{ $i }})"
                            class="flex w-full items-center justify-between gap-4 px-5 py-4 text-start">
                        <span class="font-serif text-base font-semibold text-navy-900">{{ $pergunta }}</span>
                        <svg class="h-5 w-5 flex-shrink-0 text-gold-500 transition-transform" :class="aberta === {{ $i }} && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </button>
                    <div x-show="aberta === {{ $i }}" x-collapse x-cloak>
                        <p class="px-5 pb-5 leading-relaxed text-navy-700">{{ $resposta }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <p class="text-navy-700">Ainda com dúvidas?</p>
            <a href="{{ route('site.contato') }}" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-navy-900 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-navy-800">
                Fale conosco
            </a>
        </div>
    </section>
@endsection
