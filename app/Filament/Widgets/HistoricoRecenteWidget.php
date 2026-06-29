<?php

namespace App\Filament\Widgets;

use App\Models\HistoricoCantor;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Histórico recente de músicas cantadas (Especificação Mestra, Módulo 7).
 * Log imutável (historico_cantores) — somente leitura.
 */
class HistoricoRecenteWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function getTableHeading(): string
    {
        return 'Histórico recente';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => HistoricoCantor::query()
                ->with('cantor')
                ->latest('data_culto')
                ->latest('id'))
            ->emptyStateHeading('Nenhum registro ainda')
            ->emptyStateIcon('heroicon-o-clock')
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('data_culto')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('cantor.nome')
                    ->label('Cantor')
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('nome_musica')
                    ->label('Música')
                    ->weight('bold')
                    ->searchable()
                    ->description(fn (HistoricoCantor $record): ?string => $record->artista),

                TextColumn::make('tom')
                    ->label('Tom')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('tipo_culto')
                    ->label('Culto')
                    ->badge()
                    ->color('primary')
                    ->placeholder('—'),
            ]);
    }
}
