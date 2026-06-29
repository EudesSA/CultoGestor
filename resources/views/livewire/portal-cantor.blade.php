<div class="space-y-6">

    {{-- ===== FLASH ===== --}}
    @if($flash)
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="rounded-xl px-4 py-3 text-sm font-medium flex items-center gap-3 shadow-sm
                {{ $flashTipo === 'sucesso' ? 'bg-teal-50 text-teal-800 border border-teal-200' :
                   ($flashTipo === 'erro'   ? 'bg-red-50 text-red-800 border border-red-200' :
                                              'bg-blue-50 text-blue-800 border border-blue-200') }}">
        @if($flashTipo === 'sucesso')
            <svg class="w-5 h-5 text-teal-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @elseif($flashTipo === 'erro')
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
        @else
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
        @endif
        {{ $flash }}
    </div>
    @endif

    {{-- ===== TABS ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <nav class="flex border-b border-slate-200 overflow-x-auto">
            @foreach(['perfil' => ['icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z', 'label' => 'Meu Perfil'],
                       'escalas' => ['icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5', 'label' => 'Escalas'],
                       'musicas' => ['icon' => 'M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z', 'label' => 'Músicas']] as $tab => $info)
            <button wire:click="$set('aba', '{{ $tab }}')"
                    class="flex items-center gap-2 px-5 py-4 text-sm whitespace-nowrap transition-colors flex-1 sm:flex-none justify-center sm:justify-start
                           {{ $aba === $tab ? 'tab-active bg-navy-50' : 'tab-inactive hover:bg-slate-50' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $info['icon'] }}"/>
                </svg>
                <span>{{ $info['label'] }}</span>
            </button>
            @endforeach
        </nav>

        {{-- ===== ABA: PERFIL ===== --}}
        @if($aba === 'perfil')
        <div class="p-5 sm:p-6">
            <h2 class="text-base font-semibold text-slate-800 mb-5">Dados do seu perfil</h2>
            <form wire:submit="salvarPerfil" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Nome completo *</label>
                        <input wire:model="pNome" type="text" placeholder="Seu nome"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 bg-white placeholder-slate-400
                                      focus:outline-none focus:ring-2 focus:ring-navy-700 focus:border-navy-700 transition"/>
                        @error('pNome')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">E-mail</label>
                        <input wire:model="pEmail" type="email" placeholder="seu@email.com"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 bg-white placeholder-slate-400
                                      focus:outline-none focus:ring-2 focus:ring-navy-700 focus:border-navy-700 transition"/>
                        @error('pEmail')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">WhatsApp</label>
                        <input wire:model="pTelefone" type="tel" placeholder="(00) 00000-0000"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 bg-white placeholder-slate-400
                                      focus:outline-none focus:ring-2 focus:ring-navy-700 focus:border-navy-700 transition"/>
                        @error('pTelefone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Tipo de voz</label>
                        <select wire:model="pVoz"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 bg-white
                                       focus:outline-none focus:ring-2 focus:ring-navy-700 focus:border-navy-700 transition">
                            <option value="">Selecionar...</option>
                            <option value="soprano">Soprano</option>
                            <option value="contralto">Contralto</option>
                            <option value="tenor">Tenor</option>
                            <option value="baritono">Barítono</option>
                            <option value="baixo">Baixo</option>
                            <option value="outro">Outro</option>
                        </select>
                        @error('pVoz')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 rounded-xl bg-navy-900 px-6 py-2.5 text-sm font-semibold text-white
                                   hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-navy-900 focus:ring-offset-2 transition-colors
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="salvarPerfil">Salvar perfil</span>
                        <span wire:loading wire:target="salvarPerfil">Salvando...</span>
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- ===== ABA: ESCALAS ===== --}}
        @if($aba === 'escalas')
        <div class="p-5 sm:p-6">
            <h2 class="text-base font-semibold text-slate-800 mb-1">Suas Escalas</h2>
            <p class="text-sm text-slate-500 mb-5">Cultos em que você foi convocado(a).</p>

            @if(!$this->cantor->user_id)
            <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-4 text-sm text-amber-800">
                <strong>Atenção:</strong> Seu perfil ainda não está vinculado a uma conta de usuário. As escalas aparecerão aqui assim que o administrador realizar o vínculo.
            </div>
            @elseif($this->escalas->isEmpty())
            <div class="text-center py-12 text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
                <p class="text-sm font-medium">Nenhuma escala nos próximos dias.</p>
            </div>
            @else
            <ul class="space-y-3">
                @foreach($this->escalas as $escala)
                <li class="rounded-xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-4 flex flex-col sm:flex-row sm:items-center gap-4">
                        {{-- Data --}}
                        <div class="flex-shrink-0 w-16 h-16 rounded-xl bg-navy-900 text-white flex flex-col items-center justify-center">
                            <span class="text-xs font-medium text-blue-200">{{ $escala->culto->data?->format('M') }}</span>
                            <span class="text-2xl font-bold leading-none">{{ $escala->culto->data?->format('d') }}</span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-800 text-sm">{{ $escala->culto->tipo?->nome }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $escala->culto->hora_inicio ? \Carbon\Carbon::parse($escala->culto->hora_inicio)->format('H:i') : '' }}
                                @if($escala->culto->local) · {{ $escala->culto->local }} @endif
                            </p>
                            <span class="inline-flex items-center mt-1.5 text-xs font-medium text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full">
                                {{ $escala->funcao?->nome }}
                            </span>
                        </div>

                        {{-- Status / Ações --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if($escala->status === 'confirmado')
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-teal-700 bg-teal-50 border border-teal-200 px-3 py-1.5 rounded-full">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    Confirmado
                                </span>
                            @elseif($escala->status === 'recusado')
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 px-3 py-1.5 rounded-full">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Recusado
                                </span>
                            @else
                                <button wire:click="confirmarEscala({{ $escala->id }})"
                                        class="text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 px-3 py-1.5 rounded-full transition-colors">
                                    Confirmar
                                </button>
                                <button wire:click="recusarEscala({{ $escala->id }})"
                                        class="text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-full transition-colors">
                                    Recusar
                                </button>
                            @endif
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
        @endif

        {{-- ===== ABA: MÚSICAS ===== --}}
        @if($aba === 'musicas')
        <div class="p-5 sm:p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Minhas Músicas</h2>
                    <p class="text-sm text-slate-500">Submeta músicas para os próximos cultos.</p>
                </div>
                @if(!$showFormMusica)
                <button wire:click="abrirFormMusica"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-navy-900 px-4 py-2 text-sm font-semibold text-white hover:bg-navy-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Nova música
                </button>
                @endif
            </div>

            {{-- Formulário nova música --}}
            @if($showFormMusica)
            <div class="mb-6 rounded-xl border-2 border-navy-200 bg-navy-50 p-5">
                <h3 class="text-sm font-semibold text-navy-900 mb-4">Nova proposta de música</h3>
                <form wire:submit="salvarMusica" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Culto *</label>
                        <select wire:model="mCultoId"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 bg-white
                                       focus:outline-none focus:ring-2 focus:ring-navy-700 transition">
                            <option value="">Selecionar culto...</option>
                            @foreach($this->cultosDisponiveis as $culto)
                            <option value="{{ $culto->id }}">{{ $culto->tipo?->nome }} — {{ $culto->data?->format('d/m/Y') }}</option>
                            @endforeach
                        </select>
                        @error('mCultoId')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Nome da música *</label>
                            <input wire:model="mNome" type="text" placeholder="Ex: Grande é o Senhor"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 bg-white placeholder-slate-400
                                          focus:outline-none focus:ring-2 focus:ring-navy-700 transition"/>
                            @error('mNome')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Artista / Ministério</label>
                            <input wire:model="mArtista" type="text" placeholder="Ex: Fernandinho"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 bg-white placeholder-slate-400
                                          focus:outline-none focus:ring-2 focus:ring-navy-700 transition"/>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Tom</label>
                            <input wire:model="mTom" type="text" placeholder="Ex: Sol, Ré♭, Lá"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 bg-white placeholder-slate-400
                                          focus:outline-none focus:ring-2 focus:ring-navy-700 transition"/>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Link YouTube</label>
                            <input wire:model="mLinkYoutube" type="url" placeholder="https://youtube.com/..."
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 bg-white placeholder-slate-400
                                          focus:outline-none focus:ring-2 focus:ring-navy-700 transition"/>
                            @error('mLinkYoutube')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Arquivo opcional já na criação --}}
                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Arquivo (opcional)</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Tipo</label>
                                <select wire:model="mArquivoTipo"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-navy-700 transition">
                                    <option value="mp3">MP3 — Áudio</option>
                                    <option value="playback">Playback — Instrumental</option>
                                    <option value="letra">Letra (PDF/DOC)</option>
                                    <option value="cifra">Cifra (PDF/DOC)</option>
                                    <option value="partitura">Partitura (PDF)</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Arquivo</label>
                                <input type="file" wire:model="mArquivo"
                                       accept=".mp3,.wav,.ogg,.pdf,.doc,.docx"
                                       class="block w-full text-xs text-slate-500
                                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                              file:text-xs file:font-semibold file:bg-navy-900 file:text-white
                                              hover:file:bg-navy-700 border border-slate-300 rounded-lg p-1.5 bg-white"/>
                            </div>
                        </div>
                        @if($mArquivo)
                        <p class="mt-2 text-xs text-teal-600 font-medium" wire:loading.remove wire:target="mArquivo">
                            ✓ {{ $mArquivo->getClientOriginalName() }}
                        </p>
                        @endif
                        <p class="mt-1 text-xs text-slate-400" wire:loading wire:target="mArquivo">Carregando arquivo…</p>
                        @error('mArquivo')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-slate-400">Você também pode enviar mais arquivos depois, no card da música.</p>
                    </div>

                    <div class="flex gap-3 pt-1">
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-navy-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-navy-700 transition-colors">
                            <span wire:loading.remove wire:target="salvarMusica">Enviar proposta</span>
                            <span wire:loading wire:target="salvarMusica">Enviando...</span>
                        </button>
                        <button type="button" wire:click="fecharFormMusica"
                                class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- Lista de músicas --}}
            @if($this->musicas->isEmpty())
            <div class="text-center py-12 text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z"/>
                </svg>
                <p class="text-sm font-medium">Você ainda não submeteu nenhuma música.</p>
                <p class="text-xs mt-1">Clique em "Nova música" para começar.</p>
            </div>
            @else
            <ul class="space-y-3">
                @foreach($this->musicas as $musica)
                <li class="rounded-xl border border-slate-200 overflow-hidden">
                    {{-- Header da música --}}
                    <div class="px-4 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-semibold text-slate-800 text-sm">{{ $musica->nome }}</h3>
                                    @if($musica->tom)
                                    <span class="font-mono text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-lg">{{ $musica->tom }}</span>
                                    @endif
                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full
                                        {{ match($musica->status) {
                                            'pendente'  => 'bg-amber-100 text-amber-800',
                                            'enviado'   => 'bg-blue-100 text-blue-800',
                                            'revisado'  => 'bg-purple-100 text-purple-800',
                                            'aprovado'  => 'bg-teal-100 text-teal-800',
                                            default     => 'bg-slate-100 text-slate-600',
                                        } }}">
                                        {{ ucfirst($musica->status) }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ $musica->artista }}
                                    @if($musica->artista && $musica->culto) · @endif
                                    {{ $musica->culto->tipo?->nome }} — {{ $musica->culto->data?->format('d/m/Y') }}
                                </p>
                                @if($musica->link_youtube)
                                <a href="{{ $musica->link_youtube }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-700 mt-1 font-medium">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                    Ver no YouTube
                                </a>
                                @endif

                                @if($musica->observacoes_diretor)
                                <div class="mt-2 rounded-lg bg-amber-50 border border-amber-100 px-3 py-2">
                                    <p class="text-xs font-semibold text-amber-700 mb-0.5">Observação do diretor:</p>
                                    <p class="text-xs text-amber-800">{{ $musica->observacoes_diretor }}</p>
                                </div>
                                @endif
                            </div>

                            {{-- Ações da música --}}
                            @if($musica->status !== 'aprovado')
                            <button wire:click="excluirMusica({{ $musica->id }})"
                                    wire:confirm="Remover esta música? Esta ação não pode ser desfeita."
                                    class="flex-shrink-0 text-slate-400 hover:text-red-500 transition-colors p-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                            </button>
                            @endif
                        </div>

                        {{-- Arquivos da música --}}
                        @if($musica->arquivos->isNotEmpty())
                        <div class="mt-3 pt-3 border-t border-slate-100">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Arquivos enviados</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($musica->arquivos as $arq)
                                <div class="flex items-center gap-1.5 text-xs bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 group">
                                    <span class="font-semibold
                                        {{ match($arq->tipo) {
                                            'mp3'       => 'text-teal-600',
                                            'playback'  => 'text-blue-600',
                                            'letra'     => 'text-amber-600',
                                            'cifra'     => 'text-orange-600',
                                            'partitura' => 'text-purple-600',
                                            default     => 'text-slate-500',
                                        } }}">{{ strtoupper($arq->tipo) }}</span>
                                    <span class="text-slate-600 max-w-24 truncate">{{ $arq->nome_original }}</span>
                                    @if($arq->path_local)
                                    <a href="{{ route('arquivo.musica.portal', [$cantor->token_portal, $arq->id, 'inline']) }}" target="_blank"
                                       class="text-navy-700 hover:text-navy-900 ml-0.5" title="Visualizar">👁</a>
                                    <a href="{{ route('arquivo.musica.portal', [$cantor->token_portal, $arq->id, 'download']) }}" target="_blank"
                                       class="text-slate-400 hover:text-navy-700 ml-0.5" title="Download">↓</a>
                                    @endif
                                    <button wire:click="excluirArquivo({{ $arq->id }})"
                                            wire:confirm="Remover este arquivo?"
                                            class="text-slate-300 hover:text-red-500 transition-colors ml-0.5">×</button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Botão de upload / formulário de upload --}}
                        <div class="mt-3">
                            @if($uploadMusicaId === $musica->id)
                            {{-- Formulário de upload expandido --}}
                            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 space-y-3">
                                <p class="text-xs font-semibold text-slate-600">Enviar arquivo para esta música</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-slate-500 mb-1">Tipo</label>
                                        <select wire:model="uploadTipo"
                                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-navy-700 transition">
                                            <option value="mp3">MP3 — Áudio</option>
                                            <option value="playback">Playback — Instrumental</option>
                                            <option value="letra">Letra (PDF/DOC)</option>
                                            <option value="cifra">Cifra (PDF/DOC)</option>
                                            <option value="partitura">Partitura (PDF)</option>
                                            <option value="outro">Outro</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-500 mb-1">Arquivo</label>
                                        <input type="file" wire:model="uploadArquivo"
                                               accept=".mp3,.wav,.ogg,.pdf,.doc,.docx"
                                               class="block w-full text-xs text-slate-500
                                                      file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                                      file:text-xs file:font-semibold file:bg-navy-900 file:text-white
                                                      hover:file:bg-navy-700 border border-slate-300 rounded-lg p-1.5 bg-white"/>
                                    </div>
                                </div>
                                @if($uploadArquivo)
                                <p class="text-xs text-teal-600 font-medium">
                                    ✓ {{ $uploadArquivo->getClientOriginalName() }}
                                    ({{ number_format($uploadArquivo->getSize() / 1024 / 1024, 1) }} MB)
                                </p>
                                @endif
                                @error('uploadArquivo')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                                <div class="flex gap-2">
                                    <button wire:click="enviarArquivo"
                                            wire:loading.attr="disabled"
                                            class="text-xs font-semibold bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors disabled:opacity-50">
                                        <span wire:loading.remove wire:target="enviarArquivo">Enviar</span>
                                        <span wire:loading wire:target="enviarArquivo">Enviando...</span>
                                    </button>
                                    <button wire:click="fecharUpload" type="button"
                                            class="text-xs font-medium text-slate-500 px-4 py-2 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                            @else
                            <button wire:click="abrirUpload({{ $musica->id }})"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-navy-700 border border-navy-200 bg-navy-50 hover:bg-navy-100 px-3 py-1.5 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                </svg>
                                Enviar arquivo
                            </button>
                            @endif
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
        @endif
    </div>

</div>
