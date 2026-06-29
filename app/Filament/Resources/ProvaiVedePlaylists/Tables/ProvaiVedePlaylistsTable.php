<?php

namespace App\Filament\Resources\ProvaiVedePlaylists\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProvaiVedePlaylistsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('playlist_id')
                    ->label('ID da playlist')
                    ->badge()
                    ->color('gray')
                    ->copyable(),

                IconColumn::make('ativo')
                    ->label('Ativa')
                    ->boolean(),
            ])
            ->defaultSort('nome')
            ->recordActions([
                Action::make('abrir')
                    ->label('Abrir no YouTube')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn ($record): string => 'https://www.youtube.com/playlist?list=' . $record->playlist_id)
                    ->openUrlInNewTab(),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
