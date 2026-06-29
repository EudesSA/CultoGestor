<?php

namespace App\Filament\Resources\Anuncios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AnunciosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->label('')
                    ->collection('thumbnail')
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
                    ->sortable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'anuncio' => 'info',
                        'aviso'   => 'warning',
                        default   => 'gray',
                    }),

                TextColumn::make('categoria')
                    ->label('Categoria')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('data_inicio')
                    ->label('Início')
                    ->date('d/m/Y'),

                TextColumn::make('data_fim')
                    ->label('Fim')
                    ->date('d/m/Y'),

                IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),

                IconColumn::make('sempre_disponivel')
                    ->label('Sempre')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->trueColor('warning'),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options(['anuncio' => 'Anúncio', 'aviso' => 'Aviso']),
                SelectFilter::make('categoria')
                    ->label('Categoria')
                    ->options([
                        'campal' => 'Campal', 'congresso' => 'Congresso',
                        'ja' => 'JA', 'desbravadores' => 'Desbravadores',
                        'santa_ceia' => 'Santa Ceia', 'batismo' => 'Batismo', 'geral' => 'Geral',
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
