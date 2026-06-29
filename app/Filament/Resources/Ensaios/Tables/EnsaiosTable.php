<?php

namespace App\Filament\Resources\Ensaios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EnsaiosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('data_hora')
                    ->label('Data / Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('culto')
                    ->label('Culto')
                    ->state(fn ($record): string => $record->culto
                        ? (($record->culto->tipo?->nome ?? 'Culto') . ' — ' . $record->culto->data?->format('d/m/Y'))
                        : '—')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('local')
                    ->label('Local')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('participantes_count')
                    ->label('Convocados')
                    ->counts('participantes')
                    ->alignCenter(),

                TextColumn::make('confirmados')
                    ->label('Confirmados')
                    ->state(fn ($record): int => $record->participantes()->where('status', 'confirmado')->count())
                    ->badge()
                    ->color('success')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'realizado' => 'success',
                        'cancelado' => 'danger',
                        default     => 'warning',
                    }),
            ])
            ->defaultSort('data_hora', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
