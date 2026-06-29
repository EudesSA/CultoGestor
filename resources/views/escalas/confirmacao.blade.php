<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Escala — CultoGestor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', system-ui, sans-serif; } </style>
</head>
<body class="min-h-full bg-slate-50 text-slate-900 antialiased">

    {{-- Header --}}
    <header class="bg-blue-900 shadow-lg">
        <div class="max-w-lg mx-auto px-4 py-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-teal-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
                </svg>
            </div>
            <div>
                <p class="text-white font-bold text-sm">CultoGestor</p>
                <p class="text-blue-200 text-xs">Confirmação de Escala</p>
            </div>
        </div>
    </header>

    <main class="max-w-lg mx-auto px-4 py-8">

        {{-- Flash --}}
        @if (session('mensagem'))
            @php $tipo = session('tipo', 'sucesso'); @endphp
            <div class="mb-6 rounded-xl px-4 py-3 flex items-center gap-3
                {{ $tipo === 'sucesso' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-blue-50 border border-blue-200 text-blue-800' }}">
                @if ($tipo === 'sucesso')
                    <svg class="w-5 h-5 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @else
                    <svg class="w-5 h-5 flex-shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>
                @endif
                <span class="text-sm font-medium">{{ session('mensagem') }}</span>
            </div>
        @endif

        {{-- Card da escala --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            {{-- Status banner --}}
            @php
                $statusColor = match($escala->status) {
                    'confirmado' => 'bg-green-500',
                    'recusado'   => 'bg-red-500',
                    default      => 'bg-amber-400',
                };
                $statusLabel = match($escala->status) {
                    'confirmado' => 'Confirmado',
                    'recusado'   => 'Recusado',
                    default      => 'Pendente',
                };
            @endphp
            <div class="{{ $statusColor }} px-5 py-2 flex items-center justify-between">
                <span class="text-white text-xs font-semibold uppercase tracking-wider">{{ $statusLabel }}</span>
                @if ($escala->confirmado_em)
                    <span class="text-white/80 text-xs">{{ $escala->confirmado_em->format('d/m/Y H:i') }}</span>
                @endif
            </div>

            <div class="p-6 space-y-4">

                {{-- Membro --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-blue-700 font-bold text-sm">{{ strtoupper(substr($escala->user?->name ?? '?', 0, 2)) }}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">{{ $escala->user?->name ?? '—' }}</p>
                        <p class="text-xs text-slate-500">{{ $escala->funcao?->nome ?? '—' }}</p>
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- Detalhes --}}
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Culto</p>
                        <p class="font-medium text-slate-700">{{ $escala->culto?->tipo?->nome ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Data</p>
                        <p class="font-medium text-slate-700">
                            {{ $escala->culto?->data?->translatedFormat('l, d/m/Y') ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Horário</p>
                        <p class="font-medium text-slate-700">
                            {{ $escala->culto?->hora_inicio ? \Illuminate\Support\Str::substr($escala->culto->hora_inicio, 0, 5) : '—' }}
                            @if($escala->culto?->hora_fim)
                                — {{ \Illuminate\Support\Str::substr($escala->culto->hora_fim, 0, 5) }}
                            @endif
                        </p>
                    </div>
                    @if($escala->culto?->local)
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Local</p>
                        <p class="font-medium text-slate-700">{{ $escala->culto->local }}</p>
                    </div>
                    @endif
                    @if($escala->culto?->tema)
                    <div class="col-span-2">
                        <p class="text-xs text-slate-400 mb-0.5">Tema</p>
                        <p class="font-medium text-slate-700">{{ $escala->culto->tema }}</p>
                    </div>
                    @endif
                </div>

                @if($escala->observacao)
                    <div class="bg-slate-50 rounded-lg px-4 py-3 text-sm text-slate-600">
                        <p class="text-xs text-slate-400 mb-1">Observação</p>
                        {{ $escala->observacao }}
                    </div>
                @endif
            </div>

            {{-- Ações --}}
            @if($escala->status !== 'confirmado' || $escala->status === 'recusado')
            <div class="px-6 pb-6 flex flex-col sm:flex-row gap-3">

                @if($escala->status !== 'confirmado')
                <form action="{{ route('escala.confirmar', $token) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Confirmar Presença
                    </button>
                </form>
                @endif

                @if($escala->status !== 'recusado')
                <form action="{{ route('escala.recusar', $token) }}" method="POST" class="flex-1 sm:flex-none">
                    @csrf
                    <button type="submit"
                        class="w-full sm:w-auto bg-white hover:bg-red-50 text-red-600 border border-red-200 font-medium py-3 px-5 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Não Posso Ir
                    </button>
                </form>
                @endif
            </div>
            @else
            <div class="px-6 pb-6">
                <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-center">
                    <p class="text-green-700 text-sm font-medium">Presença confirmada. Até lá!</p>
                </div>
            </div>
            @endif

            {{-- Adicionar ao Google Agenda (cada membro no seu próprio calendário) --}}
            @if($escala->culto)
            <div class="px-6 pb-6 -mt-2">
                <a href="{{ \App\Support\GoogleCalendar::linkCulto($escala->culto, 'Função: ' . ($escala->funcao?->nome ?? '')) }}"
                   target="_blank" rel="noopener"
                   class="w-full inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-medium py-3 px-4 rounded-xl transition-colors">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                    Adicionar ao meu Google Agenda
                </a>
                <p class="text-center text-xs text-slate-400 mt-2">Salva este culto no seu calendário do celular.</p>
            </div>
            @endif
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            Este link é pessoal e intransferível.
        </p>
    </main>

</body>
</html>
