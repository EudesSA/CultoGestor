{{--
    CultoGestor — Componente: Hero da página inicial
    Bloco de abertura com fundo azul institucional.

    @author Eudes S. Aguiar — ProezaTech — www.proezatech.com
    @link   https://www.proezatech.com
--}}
<section class="relative overflow-hidden bg-navy-900">
    {{-- Brilho decorativo --}}
    <div class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 rounded-full bg-navy-700/40 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-gold-500/10 blur-3xl"></div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-28">
        <div>
            <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-gold-400 ring-1 ring-white/15">
                <span class="h-1.5 w-1.5 rounded-full bg-gold-500"></span>
                Adeus, planilhas e grupos de WhatsApp
            </p>
            <h1 class="mt-6 font-serif text-4xl leading-tight font-bold tracking-tight text-white sm:text-5xl">
                A central operacional do seu culto, organizada de ponta a ponta
            </h1>
            <p class="mt-6 max-w-xl text-lg leading-relaxed text-navy-100">
                Escalas, músicas dos cantores, mídias, exportação para o Louvor JA e o Modo Culto em tempo real —
                tudo num único sistema feito para a equipe técnica e de louvor.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('site.funcionalidades') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-3 text-sm font-semibold text-navy-950 shadow-lg shadow-gold-500/20 transition-colors hover:bg-gold-400">
                    Conheça as funcionalidades
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
                <a href="{{ route('site.como-funciona') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-5 py-3 text-sm font-semibold text-white ring-1 ring-white/20 transition-colors hover:bg-white/15">
                    Ver como funciona
                </a>
            </div>
        </div>

        {{-- Cartão ilustrativo --}}
        <div class="relative">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5 shadow-2xl backdrop-blur">
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                    <span class="font-serif text-sm font-semibold text-white">Próximo culto</span>
                    <span class="rounded-full bg-gold-500/20 px-2 py-0.5 text-xs font-medium text-gold-400">Domingo · 19h</span>
                </div>
                <ul class="mt-4 space-y-3 text-sm">
                    @foreach ([
                        ['Sonoplastia', 'confirmado'],
                        ['Projeção', 'confirmado'],
                        ['Transmissão', 'pendente'],
                        ['Cantor especial', 'confirmado'],
                    ] as [$funcao, $status])
                        <li class="flex items-center justify-between rounded-lg bg-white/5 px-3 py-2">
                            <span class="text-navy-100">{{ $funcao }}</span>
                            <span @class([
                                'rounded-full px-2 py-0.5 text-xs font-medium',
                                'bg-emerald-400/15 text-emerald-300' => $status === 'confirmado',
                                'bg-amber-400/15 text-amber-300' => $status === 'pendente',
                            ])>{{ ucfirst($status) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
