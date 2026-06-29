<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Seletor de período --}}
        <div class="flex flex-wrap items-end gap-4 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mês</label>
                <select wire:model.live="mes" class="fi-input rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-600">
                    @foreach($this->meses as $num => $nome)
                        <option value="{{ $num }}">{{ $nome }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ano</label>
                <select wire:model.live="ano" class="fi-input rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-600">
                    @foreach($this->anos as $a)
                        <option value="{{ $a }}">{{ $a }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Relatórios --}}
        <div class="grid gap-4 sm:grid-cols-3">
            @php
                $params = ['mes' => $mes, 'ano' => $ano];
                $cards = [
                    ['Escala mensal por função', 'PDF · todos os escalados do mês', route('relatorios.escala', $params), 'primary'],
                    ['Participação por cantor', 'XLS · músicas cantadas no mês', route('relatorios.participacao', $params), 'success'],
                    ['Qualidade técnica', 'PDF · médias das avaliações', route('relatorios.avaliacoes', $params), 'warning'],
                ];
            @endphp

            @foreach($cards as [$titulo, $desc, $url, $cor])
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-5 flex flex-col gap-3">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $titulo }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $desc }}</p>
                </div>
                <a href="{{ $url }}" target="_blank"
                   class="mt-auto inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white transition-colors
                          {{ $cor === 'primary' ? 'bg-primary-600 hover:bg-primary-500' : ($cor === 'success' ? 'bg-green-600 hover:bg-green-500' : 'bg-amber-600 hover:bg-amber-500') }}">
                    ⬇ Baixar
                </a>
            </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
