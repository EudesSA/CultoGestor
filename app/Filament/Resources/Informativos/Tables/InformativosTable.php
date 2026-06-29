<?php

namespace App\Filament\Resources\Informativos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InformativosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_preview')
                    ->label('')
                    ->getStateUsing(fn ($record) => $record->youtube_thumbnail
                        ?: ($record->youtube_id ? "https://img.youtube.com/vi/{$record->youtube_id}/default.jpg" : null)
                    )
                    ->height(45)
                    ->width(80)
                    ->extraImgAttributes(['class' => 'rounded']),

                TextColumn::make('culto.data')
                    ->label('Culto')
                    ->formatStateUsing(fn ($state, $record) => $record->culto
                        ? ($record->culto->tipo?->nome ?? 'Culto') . ' — ' . $record->culto->data?->format('d/m/Y')
                        : '—'
                    )
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('categoria')
                    ->label('Categoria')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'missoes' => 'Missões',
                        'desbravadores' => 'Desbravadores',
                        'aventureiros' => 'Aventureiros',
                        'escola_sabatina' => 'Escola Sabatina',
                        'jovens' => 'Jovens',
                        'evangelismo' => 'Evangelismo',
                        'historias_missionarias' => 'Histórias Missionárias',
                        default => 'Outro',
                    }),

                TextColumn::make('data_exibicao')
                    ->label('Exibição')
                    ->date('d/m/Y')
                    ->sortable(),

                IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->defaultSort('data_exibicao', 'desc')
            ->filters([
                SelectFilter::make('categoria')
                    ->label('Categoria')
                    ->options([
                        'missoes' => 'Missões',
                        'desbravadores' => 'Desbravadores',
                        'aventureiros' => 'Aventureiros',
                        'escola_sabatina' => 'Escola Sabatina',
                        'jovens' => 'Jovens',
                        'evangelismo' => 'Evangelismo',
                        'historias_missionarias' => 'Histórias Missionárias',
                    ]),
            ])
            ->recordActions([
                ...\App\Filament\Support\ApresentarActions::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
