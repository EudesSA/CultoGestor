<?php

namespace App\Filament\Resources\Hinos\Tables;

use App\Filament\Support\ApresentarActions;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HinosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero')
                    ->label('Nº')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('hinario')
                    ->label('Hinário')
                    ->toggleable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('tom')
                    ->label('Tom')
                    ->badge()
                    ->placeholder('—'),

                TextColumn::make('tom_mais_tocado')
                    ->label('Tom mais tocado')
                    ->state(fn ($record): string => $record->tomMaisTocado() ?? '—')
                    ->badge()
                    ->color('success'),

                TextColumn::make('execucoes_count')
                    ->label('Execuções')
                    ->counts('execucoes')
                    ->alignCenter()
                    ->toggleable(),
            ])
            ->defaultSort('numero')
            ->recordActions([
                ...ApresentarActions::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
