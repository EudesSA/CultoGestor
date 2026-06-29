<?php

namespace App\Filament\Resources\Igrejas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IgrejasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_path')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->size(40),

                TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sigla')
                    ->label('Sigla')
                    ->badge(),

                TextColumn::make('cidade')
                    ->label('Cidade')
                    ->formatStateUsing(fn ($state, $record) => implode(' - ', array_filter([$state, $record->estado])))
                    ->searchable(),

                TextColumn::make('telefone')
                    ->label('Telefone'),

                IconColumn::make('ativa')
                    ->label('Ativa')
                    ->boolean(),
            ])
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
