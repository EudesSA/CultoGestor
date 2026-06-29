<?php

namespace App\Filament\Resources\Funcaos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class FuncaosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('nome')->label('Nome')->sortable(),
                \Filament\Tables\Columns\TextColumn::make('icone')->label('Ícone'),
                \Filament\Tables\Columns\TextColumn::make('ordem')->label('Ordem')->sortable(),
                \Filament\Tables\Columns\IconColumn::make('ativo')->label('Ativo')->boolean(),
            ])
            ->reorderable('ordem')
            ->filters([
                //
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
