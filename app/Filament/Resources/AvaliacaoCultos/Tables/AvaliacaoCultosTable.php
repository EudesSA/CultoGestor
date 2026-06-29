<?php

namespace App\Filament\Resources\AvaliacaoCultos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AvaliacaoCultosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('culto')
                    ->label('Culto')
                    ->state(fn ($record): string => $record->culto
                        ? (($record->culto->tipo?->nome ?? 'Culto') . ' — ' . $record->culto->data?->format('d/m/Y'))
                        : '—')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('user.name')
                    ->label('Avaliador')
                    ->searchable(),

                TextColumn::make('nota_geral')
                    ->label('Geral')
                    ->formatStateUsing(fn ($state): string => str_repeat('★', (int) $state))
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('media')
                    ->label('Média')
                    ->state(fn ($record): string => number_format($record->media, 1))
                    ->badge()
                    ->color('success'),

                TextColumn::make('nota_som')->label('Som')->placeholder('—')->toggleable(),
                TextColumn::make('nota_projecao')->label('Projeção')->placeholder('—')->toggleable(),
                TextColumn::make('nota_transmissao')->label('Transmissão')->placeholder('—')->toggleable(),

                TextColumn::make('created_at')
                    ->label('Em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
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
