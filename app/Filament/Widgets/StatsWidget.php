<?php

namespace App\Filament\Widgets;

use App\Models\Culto;
use App\Models\Escala;
use App\Models\Musica;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $proximoCulto  = Culto::with('tipo')
            ->where('data', '>=', today())
            ->orderBy('data')
            ->first();

        $escalas = Escala::where('status', 'pendente')
            ->whereHas('culto', fn ($q) => $q->where('data', '>=', today()))
            ->count();

        $musicasPendentes = Musica::where('status', 'enviado')->count();

        $cultosMes = Culto::whereMonth('data', now()->month)
            ->whereYear('data', now()->year)
            ->count();

        return [
            Stat::make(
                'Próximo Culto',
                $proximoCulto
                    ? $proximoCulto->data->format('d/m') . ' — ' . ($proximoCulto->tipo?->nome ?? '—')
                    : 'Nenhum agendado'
            )
                ->description($proximoCulto
                    ? ($proximoCulto->hora_inicio ? substr($proximoCulto->hora_inicio, 0, 5) : '') .
                      ($proximoCulto->tema ? ' · ' . $proximoCulto->tema : '')
                    : null)
                ->color('primary')
                ->icon('heroicon-o-calendar-days'),

            Stat::make('Escalas pendentes', $escalas)
                ->description('Aguardando confirmação')
                ->color($escalas > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-users'),

            Stat::make('Músicas para revisar', $musicasPendentes)
                ->description('Enviadas pelos cantores')
                ->color($musicasPendentes > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-musical-note'),

            Stat::make('Cultos este mês', $cultosMes)
                ->description(now()->translatedFormat('F Y'))
                ->color('info')
                ->icon('heroicon-o-calendar'),
        ];
    }
}
