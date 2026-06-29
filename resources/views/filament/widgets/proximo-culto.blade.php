@php $culto = $this->getCulto(); @endphp

@if($culto)
<div class="fi-wi-stats-overview-stat rounded-xl border border-gray-950/5 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">

    <div class="flex items-start justify-between gap-4 mb-5">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Próximo Culto — Detalhes</p>
            <p class="mt-1 text-2xl font-bold text-gray-950 dark:text-white">
                {{ $culto->tipo?->nome ?? 'Culto' }}
                <span class="text-lg font-normal text-gray-500">— {{ $culto->data->format('d/m/Y') }}</span>
            </p>
            @if($culto->tema)
            <p class="text-sm text-gray-500 mt-0.5 italic">{{ $culto->tema }}</p>
            @endif
        </div>
        <div class="flex-shrink-0 text-right">
            @if($culto->hora_inicio)
            <p class="text-2xl font-mono font-bold text-primary-600 dark:text-primary-400">
                {{ substr($culto->hora_inicio, 0, 5) }}
            </p>
            @endif
            @if($culto->local)
            <p class="text-xs text-gray-400 mt-0.5">{{ $culto->local }}</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Equipe escalada --}}
        <div>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
                Equipe ({{ $culto->escalas->count() }})
            </p>
            @forelse($culto->escalas->sortBy('funcao.ordem') as $escala)
            <div class="flex items-center justify-between py-1.5 border-b border-gray-100 dark:border-gray-800 last:border-0">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400 w-28 truncate">{{ $escala->funcao?->nome }}</span>
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $escala->user?->name }}</span>
                </div>
                @php
                    $statusConfig = match($escala->status) {
                        'confirmado' => ['text-green-600', '✓'],
                        'recusado'   => ['text-red-500', '✗'],
                        default      => ['text-amber-500', '…'],
                    };
                @endphp
                <span class="text-xs font-medium {{ $statusConfig[0] }}">{{ $statusConfig[1] }}</span>
            </div>
            @empty
            <p class="text-sm text-gray-400 italic">Nenhuma escala montada.</p>
            @endforelse
        </div>

        {{-- Músicas --}}
        <div>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
                Músicas ({{ $culto->musicas->count() }})
            </p>
            @forelse($culto->musicas as $musica)
            <div class="flex items-center justify-between py-1.5 border-b border-gray-100 dark:border-gray-800 last:border-0">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $musica->nome }}</p>
                    <p class="text-xs text-gray-400 truncate">
                        {{ $musica->cantor?->nome ?? '—' }}
                        @if($musica->tom) · <span class="font-mono">{{ strtoupper($musica->tom) }}</span> @endif
                    </p>
                </div>
                @php
                    $cor = match($musica->status) {
                        'aprovado' => 'text-green-600',
                        'enviado'  => 'text-blue-500',
                        'revisado' => 'text-amber-500',
                        default    => 'text-gray-400',
                    };
                @endphp
                <span class="ml-2 text-xs font-medium {{ $cor }} capitalize">{{ $musica->status }}</span>
            </div>
            @empty
            <p class="text-sm text-gray-400 italic">Nenhuma música cadastrada.</p>
            @endforelse
        </div>

    </div>

    <div class="mt-4 flex justify-end gap-2">
        <a href="{{ route('filament.admin.resources.cultos.edit', $culto) }}"
           class="text-xs text-primary-600 hover:text-primary-500 dark:text-primary-400 font-medium">
            Editar culto →
        </a>
        <span class="text-gray-300 dark:text-gray-700">|</span>
        <a href="{{ route('culto.modo', $culto) }}" target="_blank"
           class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-medium">
            Abrir Modo Culto ↗
        </a>
    </div>

</div>
@else
<div class="fi-wi-stats-overview-stat rounded-xl border border-gray-950/5 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900 text-center">
    <p class="text-gray-400 text-sm">Nenhum culto agendado.</p>
</div>
@endif
