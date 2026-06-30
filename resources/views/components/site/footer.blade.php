{{--
    CultoGestor — Componente Footer do site público
    Inclui o link discreto "Acesso da equipe" para o login do painel (/admin/login).

    @author Eudes S. Aguiar — ProezaTech — www.proezatech.com
    @link   https://www.proezatech.com
--}}
<footer class="mt-20 bg-navy-900 text-navy-100">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-10 md:grid-cols-3">
            {{-- Marca --}}
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-gold-500">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z"/>
                        </svg>
                    </span>
                    <span class="font-serif text-lg font-bold text-white">CultoGestor</span>
                </div>
                <p class="mt-4 max-w-xs text-sm leading-relaxed text-navy-200">
                    Central operacional do culto: escalas, músicas, mídias, Louvor JA e Modo Culto — tudo em um só lugar.
                </p>
            </div>

            {{-- Navegação --}}
            <div>
                <h3 class="text-sm font-semibold tracking-wide text-white uppercase">Navegação</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('site.funcionalidades') }}" class="text-navy-200 hover:text-white">Funcionalidades</a></li>
                    <li><a href="{{ route('site.como-funciona') }}" class="text-navy-200 hover:text-white">Como funciona</a></li>
                    <li><a href="{{ route('site.sobre') }}" class="text-navy-200 hover:text-white">Sobre</a></li>
                    <li><a href="{{ route('site.faq') }}" class="text-navy-200 hover:text-white">Perguntas frequentes</a></li>
                    <li><a href="{{ route('site.contato') }}" class="text-navy-200 hover:text-white">Contato</a></li>
                </ul>
            </div>

            {{-- Projeto / colaboração --}}
            <div>
                <h3 class="text-sm font-semibold tracking-wide text-white uppercase">Projeto</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><span class="text-navy-200">Software livre para equipes de louvor</span></li>
                    <li><a href="https://www.proezatech.com" target="_blank" rel="noopener" class="text-navy-200 hover:text-white">Desenvolvido por ProezaTech</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-6 sm:flex-row">
            <p class="text-xs text-navy-300">
                &copy; {{ date('Y') }} CultoGestor · Eudes S. Aguiar — ProezaTech
            </p>
            {{-- Link discreto de acesso da equipe (sem destaque de CTA) --}}
            <a href="{{ url('/admin/login') }}" class="text-xs text-navy-300 underline-offset-4 transition-colors hover:text-navy-100 hover:underline">
                Acesso da equipe &rarr;
            </a>
        </div>
    </div>
</footer>
