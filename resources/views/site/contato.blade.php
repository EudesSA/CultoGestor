{{--
    CultoGestor — Página: Contato (formulário público)

    @author Eudes S. Aguiar — ProezaTech — www.proezatech.com
    @link   https://www.proezatech.com
--}}
@extends('layouts.site')

@section('title', 'Contato')
@section('meta_description', 'Fale com o time do CultoGestor. Tire dúvidas, peça uma demonstração ou proponha colaboração no projeto.')

@section('content')
    {{-- Cabeçalho --}}
    <section class="bg-navy-900">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold tracking-wide text-gold-400 uppercase">Contato</p>
            <h1 class="mt-2 font-serif text-4xl font-bold tracking-tight text-white sm:text-5xl">Vamos conversar</h1>
            <p class="mt-4 max-w-2xl text-lg text-navy-100">
                Dúvidas, demonstração ou interesse em colaborar com o projeto? Escreva pra gente.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8">
        {{-- Mensagem de sucesso --}}
        @if (session('contato_ok'))
            <div class="mb-8 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4">
                <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-emerald-800">Mensagem enviada! Em breve entraremos em contato.</p>
            </div>
        @endif

        <form method="POST" action="{{ route('site.contato.enviar') }}" class="space-y-6">
            @csrf

            {{-- Honeypot anti-spam (oculto; bots preenchem, humanos não) --}}
            <div class="hidden" aria-hidden="true">
                <label>Não preencha este campo<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
            </div>

            <div>
                <label for="nome" class="block text-sm font-medium text-navy-900">Nome</label>
                <input type="text" id="nome" name="nome" value="{{ old('nome') }}" required
                       class="mt-1.5 block w-full rounded-lg border border-navy-200 bg-white px-4 py-2.5 text-navy-900 shadow-sm focus:border-navy-500 focus:ring-2 focus:ring-navy-500/30 focus:outline-none @error('nome') border-red-400 @enderror">
                @error('nome') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-navy-900">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="mt-1.5 block w-full rounded-lg border border-navy-200 bg-white px-4 py-2.5 text-navy-900 shadow-sm focus:border-navy-500 focus:ring-2 focus:ring-navy-500/30 focus:outline-none @error('email') border-red-400 @enderror">
                @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="mensagem" class="block text-sm font-medium text-navy-900">Mensagem</label>
                <textarea id="mensagem" name="mensagem" rows="5" required
                          class="mt-1.5 block w-full rounded-lg border border-navy-200 bg-white px-4 py-2.5 text-navy-900 shadow-sm focus:border-navy-500 focus:ring-2 focus:ring-navy-500/30 focus:outline-none @error('mensagem') border-red-400 @enderror">{{ old('mensagem') }}</textarea>
                @error('mensagem') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-3 text-sm font-semibold text-navy-950 shadow-lg shadow-gold-500/20 transition-colors hover:bg-gold-400">
                Enviar mensagem
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
            </button>
        </form>
    </section>
@endsection
