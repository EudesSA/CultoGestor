@php
$tipoLabel = [
    'musica'     => 'Música',
    'video'      => 'Vídeo',
    'anuncio'    => 'Anúncio',
    'informativo'=> 'Informativo',
    'hino'       => 'Hino',
    'item_livre' => 'Item Livre',
    'oracao'     => 'Oração',
    'sermao'     => 'Sermão',
    'ofertorio'  => 'Ofertório',
];

$tipoIcone = [
    'musica'     => '🎵',
    'video'      => '▶',
    'anuncio'    => '📢',
    'informativo'=> 'ℹ',
    'hino'       => '📖',
    'item_livre' => '•',
    'oracao'     => '🙏',
    'sermao'     => '📋',
    'ofertorio'  => '💛',
];

$atual = $this->liturgiaAtiva;
$ref   = $atual?->referencia;
$ytId  = $this->youtubeId;
$liturgias = $this->liturgias;
$idxAtual  = $liturgias->search(fn ($l) => $l->id === $this->liturgiaAtivaId);
$temAnterior = $idxAtual !== false && $idxAtual > 0;
$temProximo  = $idxAtual !== false && isset($liturgias[$idxAtual + 1]);
@endphp

<div class="flex h-screen"
     x-data="{}"
     @keydown.arrow-right.window="$wire.avancar()"
     @keydown.arrow-left.window="$wire.voltar()"
     @keydown.enter.window="$wire.marcarConcluido()"
     @keydown.space.window.prevent="$wire.marcarConcluido()">

    {{-- ===== SIDEBAR: Lista da Liturgia ===== --}}
    <aside class="w-72 bg-gray-900 border-r border-gray-800 flex flex-col flex-shrink-0">

        {{-- Cabeçalho do culto --}}
        <div class="px-4 py-4 border-b border-gray-800 bg-gray-950">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                <span class="text-xs font-semibold text-green-400 uppercase tracking-wide">Ao Vivo</span>
            </div>
            <p class="text-sm font-bold text-white leading-tight">
                {{ $this->culto->tipo?->nome ?? 'Culto' }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">
                {{ $this->culto->data?->format('d/m/Y') }}
                @if($this->culto->hora_inicio)
                    · {{ substr($this->culto->hora_inicio, 0, 5) }}
                @endif
            </p>
            @if($this->culto->tema)
            <p class="text-xs text-indigo-300 mt-1 italic truncate">{{ $this->culto->tema }}</p>
            @endif
        </div>

        {{-- Lista de itens --}}
        <nav class="flex-1 overflow-y-auto scrollbar-thin py-1">
            @forelse($liturgias as $i => $liturgia)
            <button
                wire:click="ativarItem({{ $liturgia->id }})"
                wire:key="item-{{ $liturgia->id }}"
                class="w-full text-left px-3 py-2.5 border-b border-gray-800/50 transition-colors
                    @if($liturgia->id === $this->liturgiaAtivaId)
                        bg-indigo-900/60 border-l-2 border-l-indigo-400
                    @else
                        hover:bg-gray-800/60 border-l-2 border-l-transparent
                    @endif
                    @if($liturgia->concluido) opacity-50 @endif">
                <div class="flex items-start gap-2">
                    <span class="text-xs text-gray-600 w-5 mt-0.5 flex-shrink-0 text-right">{{ $liturgia->ordem }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs">{{ $tipoIcone[$liturgia->tipo] ?? '•' }}</span>
                            <p class="text-xs font-medium text-white truncate">{{ $liturgia->titulo }}</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">
                            {{ $tipoLabel[$liturgia->tipo] ?? $liturgia->tipo }}
                            @if($liturgia->duracao_minutos) · {{ $liturgia->duracao_minutos }}min @endif
                            @if($liturgia->horario_previsto) · {{ substr($liturgia->horario_previsto, 0, 5) }} @endif
                        </p>
                    </div>
                    @if($liturgia->concluido)
                    <span class="text-green-400 text-xs flex-shrink-0 mt-0.5">✓</span>
                    @elseif($liturgia->id === $this->liturgiaAtivaId)
                    <span class="text-indigo-400 text-xs flex-shrink-0 mt-0.5">▶</span>
                    @endif
                </div>
            </button>
            @empty
            <p class="text-xs text-gray-600 text-center py-8">Nenhum item na liturgia.</p>
            @endforelse
        </nav>

        {{-- ===== Banco de Hinos: busca por número (Fase 9) ===== --}}
        @php $hino = $this->hinoEncontrado; @endphp
        <div class="border-t border-gray-800 bg-gray-950/60 flex-shrink-0 px-3 py-2">
            <p class="pb-1 text-xs font-semibold text-indigo-400/80 uppercase tracking-wide">📖 Banco de Hinos</p>
            <input type="number" min="1" wire:model.live.debounce.400ms="buscaHino"
                   placeholder="Nº do hino…"
                   class="w-full bg-gray-800 border border-gray-700 rounded-md px-2.5 py-1.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500">
            @if($hino)
            <div class="mt-2 rounded-md bg-indigo-900/30 border border-indigo-800/50 px-2.5 py-2">
                <p class="text-sm font-medium text-white leading-tight">#{{ $hino->numero }} — {{ $hino->titulo }}</p>
                <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                    @if($hino->tomMaisTocado())
                    <span class="text-xs px-1.5 py-0.5 rounded bg-green-900/50 text-green-300 font-mono font-bold">
                        Tom: {{ $hino->tomMaisTocado() }}
                    </span>
                    @endif
                    @if($hino->execucoes_count ?? $hino->execucoes()->count())
                    <span class="text-xs text-gray-500">{{ $hino->execucoes()->count() }}× tocado</span>
                    @endif
                </div>
                <div class="flex items-center gap-2 mt-2">
                    @if($hino->temApresentacao())
                    <a href="{{ $hino->urlApresentacao() }}" target="_blank"
                       class="text-xs px-2 py-1 rounded bg-sky-700 text-white hover:bg-sky-600">🖥 Apresentar</a>
                    @endif
                    <button wire:click="registrarHinoExecucao"
                            class="text-xs px-2 py-1 rounded bg-gray-700 text-white hover:bg-gray-600">+ Registrar execução</button>
                </div>
            </div>
            @elseif(strlen(trim($buscaHino)) > 0)
            <p class="mt-2 text-xs text-gray-500">Hino não encontrado.</p>
            @endif
        </div>

        {{-- ===== Anúncios: "Exibir Agora" a qualquer momento (M5) ===== --}}
        @php $anuncios = $this->anunciosDisponiveis; @endphp
        @if($anuncios->isNotEmpty())
        <div class="border-t border-gray-800 bg-gray-950/60 flex-shrink-0">
            <p class="px-3 pt-2 pb-1 text-xs font-semibold text-yellow-500/80 uppercase tracking-wide flex items-center gap-1">
                📢 Anúncios ativos
            </p>
            <div class="max-h-44 overflow-y-auto scrollbar-thin px-2 pb-2 space-y-1">
                @foreach($anuncios as $anuncio)
                <button
                    wire:click="exibirAnuncio({{ $anuncio->id }})"
                    wire:key="anuncio-{{ $anuncio->id }}"
                    class="w-full text-left px-2.5 py-1.5 rounded-md bg-gray-800/60 hover:bg-yellow-900/40 border border-gray-700/50 hover:border-yellow-700/50 transition-colors group">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-xs font-medium text-gray-200 truncate">{{ $anuncio->titulo }}</p>
                        <span class="text-xs text-yellow-500/70 group-hover:text-yellow-400 flex-shrink-0 font-semibold">Exibir →</span>
                    </div>
                    @if($anuncio->categoria)
                    <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $anuncio->categoria }}</p>
                    @endif
                </button>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Rodapé sidebar --}}
        <div class="px-3 py-2 border-t border-gray-800 bg-gray-950">
            <p class="text-xs text-gray-600 text-center">
                {{ $liturgias->where('concluido', true)->count() }}/{{ $liturgias->count() }} concluídos
            </p>
        </div>
    </aside>

    {{-- ===== ÁREA PRINCIPAL ===== --}}
    <main class="flex-1 flex flex-col min-w-0">

        {{-- Conteúdo do item ativo --}}
        <div class="flex-1 flex flex-col items-center justify-center overflow-auto p-8 min-h-0">

            @if($atual)

                {{-- Badge do tipo --}}
                <div class="mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-800 text-gray-300 border border-gray-700">
                        <span>{{ $tipoIcone[$atual->tipo] ?? '•' }}</span>
                        {{ $tipoLabel[$atual->tipo] ?? $atual->tipo }}
                        @if($atual->horario_previsto)
                            <span class="text-gray-500">· {{ substr($atual->horario_previsto, 0, 5) }}</span>
                        @endif
                    </span>
                </div>

                {{-- Título --}}
                <h1 class="text-4xl md:text-5xl font-bold text-white text-center mb-4 leading-tight max-w-4xl">
                    {{ $atual->titulo }}
                </h1>

                {{-- Detalhes específicos por tipo --}}
                @if($atual->tipo === 'musica' && $ref instanceof \App\Models\Musica)
                    <div class="flex items-center gap-4 text-gray-400 text-sm mb-6 flex-wrap justify-center">
                        @if($ref->artista)
                        <span>{{ $ref->artista }}</span>
                        @endif
                        @if($ref->tom)
                        <span class="px-2 py-0.5 bg-indigo-900/50 rounded text-indigo-300 font-mono font-bold text-base">
                            {{ strtoupper($ref->tom) }}
                        </span>
                        @endif
                        @if($ref->cantor?->nome)
                        <span class="flex items-center gap-1 text-teal-400">
                            🎤 {{ $ref->cantor->nome }}
                        </span>
                        @endif
                    </div>

                @elseif(in_array($atual->tipo, ['video', 'informativo']) && $ref)
                    @php
                        $tema = $ref->tema ?? null;
                        $canal = $ref->youtube_canal ?? null;
                        $durSeg = $ref->duracao_segundos ?? null;
                    @endphp
                    <div class="flex items-center gap-3 text-gray-400 text-sm mb-6 flex-wrap justify-center">
                        @if($tema) <span>{{ $tema }}</span> @endif
                        @if($canal) <span class="text-red-400">▶ {{ $canal }}</span> @endif
                        @if($durSeg) <span>{{ gmdate('i:s', $durSeg) }}</span> @endif
                    </div>

                @elseif($atual->tipo === 'anuncio' && $ref instanceof \App\Models\Anuncio)
                    @if($ref->descricao)
                    <p class="text-gray-400 text-base text-center max-w-2xl mb-6">{{ $ref->descricao }}</p>
                    @endif
                    @if($ref->categoria)
                    <span class="px-2 py-0.5 bg-yellow-900/30 border border-yellow-700/30 rounded text-yellow-400 text-xs mb-6">
                        {{ $ref->categoria }}
                    </span>
                    @endif
                @endif

                {{-- Player YouTube --}}
                @if($ytId)
                <div class="w-full max-w-3xl aspect-video rounded-lg overflow-hidden shadow-2xl mb-4">
                    <iframe
                        src="https://www.youtube.com/embed/{{ $ytId }}?rel=0&modestbranding=1"
                        class="w-full h-full"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
                @endif

                {{-- Observação --}}
                @if($atual->observacao)
                <p class="text-gray-500 text-sm text-center max-w-2xl mt-4 italic">
                    {{ $atual->observacao }}
                </p>
                @endif

            @else
                <div class="text-center">
                    <p class="text-6xl mb-4">⛪</p>
                    <p class="text-gray-500 text-lg">Selecione um item da liturgia</p>
                </div>
            @endif

        </div>

        {{-- ===== BARRA DE CONTROLES ===== --}}
        <div class="bg-gray-900 border-t border-gray-800 px-6 py-3 flex items-center justify-between gap-4 flex-shrink-0">

            {{-- Navegação --}}
            <div class="flex items-center gap-2">
                <button
                    wire:click="voltar"
                    @class(['flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                            'bg-gray-800 text-white hover:bg-gray-700' => $temAnterior,
                            'bg-gray-900 text-gray-700 cursor-not-allowed' => !$temAnterior])
                    @disabled(!$temAnterior)>
                    ← Anterior
                </button>
                <button
                    wire:click="avancar"
                    @class(['flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                            'bg-indigo-600 text-white hover:bg-indigo-500' => $temProximo,
                            'bg-gray-900 text-gray-700 cursor-not-allowed' => !$temProximo])
                    @disabled(!$temProximo)>
                    Próximo →
                </button>
            </div>

            {{-- Contador de posição --}}
            <div class="text-sm text-gray-500">
                @if($idxAtual !== false)
                    <span class="text-white font-semibold">{{ $idxAtual + 1 }}</span>
                    <span class="text-gray-600">/</span>
                    <span>{{ $liturgias->count() }}</span>
                @endif
            </div>

            {{-- Ações --}}
            <div class="flex items-center gap-3">
                @if($ref && method_exists($ref, 'temApresentacao') && $ref->temApresentacao())
                <a href="{{ $ref->urlApresentacao() }}" target="_blank"
                   class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-sky-700 text-white hover:bg-sky-600 transition-colors">
                    🖥 Apresentar em monitor
                </a>
                @endif

                @if($atual && !$atual->concluido)
                <button
                    wire:click="marcarConcluido"
                    class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-green-700 text-white hover:bg-green-600 transition-colors">
                    ✓ Concluído
                </button>
                @elseif($atual?->concluido)
                <span class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-800 text-green-400">
                    ✓ Concluído
                </span>
                @endif

                {{-- Atalhos de teclado --}}
                <div class="hidden lg:flex items-center gap-2 text-xs text-gray-600 ml-2">
                    <kbd class="px-1.5 py-0.5 bg-gray-800 rounded border border-gray-700">←</kbd>
                    <span>Anterior</span>
                    <kbd class="px-1.5 py-0.5 bg-gray-800 rounded border border-gray-700">→</kbd>
                    <span>Próximo</span>
                    <kbd class="px-1.5 py-0.5 bg-gray-800 rounded border border-gray-700">Enter</kbd>
                    <span>Concluir</span>
                </div>
            </div>
        </div>

    </main>

    {{-- ===== OVERLAY "Exibir Agora": anúncio fullscreen ===== --}}
    @php $anuncioOverlay = $this->anuncioAtivo; @endphp
    @if($anuncioOverlay)
    @php $thumbUrl = $anuncioOverlay->getFirstMediaUrl('thumbnail'); @endphp
    <div class="fixed inset-0 z-50 bg-black flex flex-col"
         x-data
         @keydown.escape.window="$wire.fecharAnuncio()">

        {{-- Ações do overlay --}}
        <div class="absolute top-4 right-4 z-10 flex items-center gap-2">
            @if($anuncioOverlay->temApresentacao())
            <a href="{{ $anuncioOverlay->urlApresentacao() }}" target="_blank"
               class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-sky-700/90 text-white hover:bg-sky-600 transition-colors backdrop-blur">
                🖥 Apresentar em monitor
            </a>
            @endif
            <button
                wire:click="fecharAnuncio"
                class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-gray-800/80 text-white hover:bg-gray-700 transition-colors backdrop-blur">
                ✕ Fechar <kbd class="hidden md:inline px-1.5 py-0.5 bg-gray-900 rounded border border-gray-700 text-xs">Esc</kbd>
            </button>
        </div>

        {{-- Conteúdo do anúncio --}}
        <div class="flex-1 flex flex-col items-center justify-center p-8 overflow-auto">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-900/40 text-yellow-300 border border-yellow-700/40 mb-6">
                📢 {{ ucfirst($anuncioOverlay->tipo ?? 'Anúncio') }}
                @if($anuncioOverlay->categoria) · {{ $anuncioOverlay->categoria }} @endif
            </span>

            <h1 class="text-4xl md:text-6xl font-bold text-white text-center mb-6 leading-tight max-w-5xl">
                {{ $anuncioOverlay->titulo }}
            </h1>

            @if($thumbUrl)
            <img src="{{ $thumbUrl }}" alt="{{ $anuncioOverlay->titulo }}"
                 class="max-h-[60vh] max-w-full rounded-lg shadow-2xl object-contain mb-6">
            @endif

            @if($anuncioOverlay->descricao)
            <p class="text-gray-300 text-lg md:text-xl text-center max-w-3xl leading-relaxed">
                {{ $anuncioOverlay->descricao }}
            </p>
            @endif

            @if(! $thumbUrl && $anuncioOverlay->midias()->exists())
            <p class="text-gray-500 text-sm mt-6 italic">
                Mídias deste anúncio estão na pasta do culto no Drive ({{ $anuncioOverlay->midias()->count() }} arquivo(s)).
            </p>
            @endif
        </div>
    </div>
    @endif

</div>
