<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between w-full">
                <span>Calendário de Programações</span>
                <div class="flex items-center gap-2 text-sm font-normal">
                    <button wire:click="mesAnterior"
                        class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                        </svg>
                    </button>
                    <span class="font-semibold text-gray-700 dark:text-gray-200 min-w-36 text-center">
                        {{ $this->diasNoMes['mesLabel'] }}
                    </span>
                    <button wire:click="proximoMes"
                        class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                        </svg>
                    </button>
                </div>
            </div>
        </x-slot>

        @php
            $cultos   = collect($this->cultos)->groupBy('dia');
            $total    = $this->diasNoMes['total'];
            $offset   = $this->diasNoMes['offset'];
            $hoje     = now()->format('Y-m-d');
            $semanas  = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        @endphp

        {{-- Cabeçalho dos dias da semana --}}
        <div class="grid grid-cols-7 gap-px mb-1">
            @foreach($semanas as $dia)
                <div class="text-center text-xs font-semibold text-gray-400 dark:text-gray-500 py-1">{{ $dia }}</div>
            @endforeach
        </div>

        {{-- Grade do calendário --}}
        <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-white/10 rounded-xl overflow-hidden">
            {{-- Células vazias do offset --}}
            @for($i = 0; $i < $offset; $i++)
                <div class="bg-gray-50 dark:bg-gray-900/50 min-h-[80px]"></div>
            @endfor

            {{-- Dias do mês --}}
            @for($dia = 1; $dia <= $total; $dia++)
                @php
                    $dataStr  = $mesAtual . '-' . str_pad($dia, 2, '0', STR_PAD_LEFT);
                    $ehHoje   = $dataStr === $hoje;
                    $diaItems = $cultos->get($dia, collect());
                @endphp
                <div class="bg-white dark:bg-gray-900 min-h-[80px] p-1.5 relative
                    {{ $ehHoje ? 'ring-2 ring-inset ring-blue-500' : '' }}">

                    {{-- Número do dia --}}
                    <div class="flex justify-end mb-1">
                        <span class="text-xs w-6 h-6 flex items-center justify-center rounded-full font-medium
                            {{ $ehHoje
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-500 dark:text-gray-400' }}">
                            {{ $dia }}
                        </span>
                    </div>

                    {{-- Cultos do dia --}}
                    <div class="space-y-0.5">
                        @foreach($diaItems as $culto)
                            <a href="{{ $culto['url'] }}"
                                class="block rounded px-1.5 py-0.5 text-xs text-white font-medium truncate leading-tight hover:opacity-90 transition-opacity"
                                style="background-color: {{ $culto['cor'] }};"
                                title="{{ $culto['tipo'] }}{{ $culto['hora'] ? ' · ' . $culto['hora'] : '' }}{{ $culto['tema'] ? ' · ' . $culto['tema'] : '' }}">
                                @if($culto['hora'])
                                    <span class="opacity-80">{{ $culto['hora'] }}</span>
                                @endif
                                {{ $culto['tipo'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endfor

            {{-- Células vazias no final para completar a última semana --}}
            @php $celulasRestantes = (7 - ($offset + $total) % 7) % 7; @endphp
            @for($i = 0; $i < $celulasRestantes; $i++)
                <div class="bg-gray-50 dark:bg-gray-900/50 min-h-[80px]"></div>
            @endfor
        </div>

        {{-- Legenda de status --}}
        <div class="mt-3 flex flex-wrap gap-3 text-xs text-gray-500 dark:text-gray-400">
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> Hoje
            </span>
            <span class="flex items-center gap-1.5">
                Clique em um evento para editar
            </span>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
