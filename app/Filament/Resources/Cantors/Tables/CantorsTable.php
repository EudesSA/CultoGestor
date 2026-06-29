<?php

namespace App\Filament\Resources\Cantors\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CantorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('voz')
                    ->label('Voz')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'soprano'   => 'Soprano',
                        'contralto' => 'Contralto',
                        'tenor'     => 'Tenor',
                        'baritono'  => 'Barítono',
                        'baixo'     => 'Baixo',
                        default     => 'Outro',
                    }),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('telefone')
                    ->label('WhatsApp'),

                TextColumn::make('musicas_count')
                    ->label('Músicas')
                    ->counts('musicas')
                    ->badge()
                    ->color('info'),

                IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('voz')
                    ->label('Voz')
                    ->options([
                        'soprano'   => 'Soprano',
                        'contralto' => 'Contralto',
                        'tenor'     => 'Tenor',
                        'baritono'  => 'Barítono',
                        'baixo'     => 'Baixo',
                    ]),
                TernaryFilter::make('ativo')->label('Apenas ativos'),
            ])
            ->recordActions([
                Action::make('portal')
                    ->label('Portal')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->modalHeading('Portal do Cantor')
                    ->modalContent(fn ($record) => view('filament.cantor-portal-modal', [
                        'cantor' => $record,
                        'url'    => $record->portalUrl(),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fechar'),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
