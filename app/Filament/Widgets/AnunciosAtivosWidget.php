<?php

namespace App\Filament\Widgets;

use App\Models\Anuncio;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Anúncios ativos hoje (Especificação Mestra, Módulo 7).
 * Mostra QUAIS anúncios estão no ar — mais útil no dia do culto que um contador.
 */
class AnunciosAtivosWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function getTableHeading(): string
    {
        return 'Anúncios ativos hoje';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Anuncio::query()->ativos()->orderByDesc('sempre_disponivel')->orderBy('ordem'))
            ->emptyStateHeading('Nenhum anúncio ativo hoje')
            ->emptyStateIcon('heroicon-o-megaphone')
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'anuncio' => 'info',
                        'aviso'   => 'warning',
                        default   => 'gray',
                    }),

                TextColumn::make('categoria')
                    ->label('Categoria')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('periodo')
                    ->label('Período')
                    ->state(function (Anuncio $record): string {
                        if ($record->sempre_disponivel) {
                            return 'Sempre disponível';
                        }
                        $ini = $record->data_inicio?->format('d/m/Y') ?? '—';
                        $fim = $record->data_fim?->format('d/m/Y') ?? '—';
                        return "{$ini} → {$fim}";
                    })
                    ->badge()
                    ->color(fn (Anuncio $record): string => $record->sempre_disponivel ? 'success' : 'primary'),
            ]);
    }
}
