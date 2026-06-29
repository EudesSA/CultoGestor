<?php

namespace App\Filament\Resources\ProvaiVedes\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProvaiVedesTable
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
                    ->description(fn($record) => $record->tema)
                    ->wrap(),

                TextColumn::make('categoria')
                    ->label('Categoria')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'saude' => 'Saúde',
                        'familia' => 'Família',
                        'alimentacao' => 'Alimentação',
                        'espiritualidade' => 'Espiritualidade',
                        'educacao' => 'Educação',
                        'relacionamentos' => 'Relacionamentos',
                        'financas' => 'Finanças',
                        default => 'Outro',
                    }),

                TextColumn::make('data_exibicao')
                    ->label('Exibição')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('trimestre')
                    ->label('Trimestre'),

                IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->defaultSort('data_exibicao', 'desc')
            ->filters([
                SelectFilter::make('categoria')
                    ->label('Categoria')
                    ->options([
                        'saude' => 'Saúde',
                        'familia' => 'Família',
                        'alimentacao' => 'Alimentação',
                        'espiritualidade' => 'Espiritualidade',
                        'educacao' => 'Educação',
                        'relacionamentos' => 'Relacionamentos',
                        'financas' => 'Finanças',
                    ]),
                TernaryFilter::make('ativo')->label('Apenas ativos'),
                Filter::make('favoritos')
                    ->label('Meus favoritos')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereHas(
                        'favoritadoPor',
                        fn (Builder $q) => $q->where('user_id', auth()->id())
                    )),
                Filter::make('pendentes_importacao')
                    ->label('Pendentes de aprovação (scraper)')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('status_importacao', 'pendente_aprovacao')),
            ])
            ->recordActions([
                Action::make('aprovar')
                    ->label('Aprovar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record): bool => $record->status_importacao === 'pendente_aprovacao')
                    ->action(fn ($record) => $record->update([
                        'status_importacao' => 'ativo',
                        'ativo'             => true,
                    ])),
                Action::make('favoritar')
                    ->label(fn ($record): string => $record->isFavorito() ? 'Favorito' : 'Favoritar')
                    ->icon(fn ($record): string => $record->isFavorito() ? 'heroicon-s-heart' : 'heroicon-o-heart')
                    ->color(fn ($record): string => $record->isFavorito() ? 'danger' : 'gray')
                    ->iconButton()
                    ->action(fn ($record) => $record->alternarFavorito()),
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
