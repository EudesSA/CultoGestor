<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Ensaio — CultoGestor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', system-ui, sans-serif; } </style>
</head>
<body class="min-h-full bg-slate-50 text-slate-900 antialiased">

    <header class="bg-indigo-900 shadow-lg">
        <div class="max-w-lg mx-auto px-4 py-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">🎼</div>
            <div>
                <p class="text-white font-bold text-sm">CultoGestor</p>
                <p class="text-indigo-200 text-xs">Confirmação de Ensaio</p>
            </div>
        </div>
    </header>

    <main class="max-w-lg mx-auto px-4 py-8">

        @if (session('mensagem'))
            @php $tipo = session('tipo', 'sucesso'); @endphp
            <div class="mb-6 rounded-xl px-4 py-3 text-sm font-medium
                {{ $tipo === 'sucesso' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-blue-50 border border-blue-200 text-blue-800' }}">
                {{ session('mensagem') }}
            </div>
        @endif

        @php $ensaio = $participante->ensaio; @endphp

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            @php
                $statusColor = match($participante->status) {
                    'confirmado' => 'bg-green-500', 'recusado' => 'bg-red-500', default => 'bg-amber-400',
                };
                $statusLabel = match($participante->status) {
                    'confirmado' => 'Confirmado', 'recusado' => 'Recusado', default => 'Pendente',
                };
            @endphp
            <div class="{{ $statusColor }} px-5 py-2 flex items-center justify-between">
                <span class="text-white text-xs font-semibold uppercase tracking-wider">{{ $statusLabel }}</span>
                @if ($participante->confirmado_em)
                    <span class="text-white/80 text-xs">{{ $participante->confirmado_em->format('d/m/Y H:i') }}</span>
                @endif
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <p class="font-semibold text-slate-800 text-lg">Olá, {{ $participante->user?->name ?? 'membro' }}!</p>
                    <p class="text-sm text-slate-500">Você foi convocado para um ensaio.</p>
                </div>

                <hr class="border-slate-100">

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Data / Hora</p>
                        <p class="font-medium text-slate-700">{{ $ensaio->data_hora?->translatedFormat('l, d/m/Y H:i') ?? '—' }}</p>
                    </div>
                    @if($ensaio->local)
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Local</p>
                        <p class="font-medium text-slate-700">{{ $ensaio->local }}</p>
                    </div>
                    @endif
                    @if($ensaio->culto)
                    <div class="col-span-2">
                        <p class="text-xs text-slate-400 mb-0.5">Prepara para o culto</p>
                        <p class="font-medium text-slate-700">{{ ($ensaio->culto->tipo?->nome ?? 'Culto') . ' — ' . $ensaio->culto->data?->format('d/m/Y') }}</p>
                    </div>
                    @endif
                </div>

                @if($ensaio->musicas->isNotEmpty())
                <div class="bg-slate-50 rounded-lg px-4 py-3">
                    <p class="text-xs text-slate-400 mb-2">Repertório do ensaio</p>
                    <ul class="space-y-1">
                        @foreach($ensaio->musicas as $m)
                        <li class="text-sm text-slate-700 flex items-center gap-2">
                            <span class="text-slate-400">{{ $loop->iteration }}.</span>
                            {{ $m->nome }}
                            @if($m->tom)<span class="text-xs px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700 font-mono">{{ $m->tom }}</span>@endif
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($ensaio->observacoes)
                <div class="bg-slate-50 rounded-lg px-4 py-3 text-sm text-slate-600">
                    <p class="text-xs text-slate-400 mb-1">Observações</p>
                    {{ $ensaio->observacoes }}
                </div>
                @endif
            </div>

            <div class="px-6 pb-6 flex flex-col sm:flex-row gap-3">
                @if($participante->status !== 'confirmado')
                <form action="{{ route('ensaio.confirmar', $token) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors">
                        ✓ Confirmar Presença
                    </button>
                </form>
                @endif
                @if($participante->status !== 'recusado')
                <form action="{{ route('ensaio.recusar', $token) }}" method="POST" class="flex-1 sm:flex-none">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto bg-white hover:bg-red-50 text-red-600 border border-red-200 font-medium py-3 px-5 rounded-xl transition-colors">
                        ✕ Não Posso Ir
                    </button>
                </form>
                @endif
            </div>

            <div class="px-6 pb-6 -mt-2">
                <a href="{{ \App\Support\GoogleCalendar::linkEnsaio($ensaio) }}" target="_blank" rel="noopener"
                   class="w-full inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-medium py-3 px-4 rounded-xl transition-colors">
                    📅 Adicionar ao meu Google Agenda
                </a>
            </div>
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">Este link é pessoal e intransferível.</p>
    </main>
</body>
</html>
