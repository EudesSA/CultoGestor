<?php

namespace App\Filament\Resources\CultoTipos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class CultoTiposTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('nome')->label('Nome')->sortable(),
                \Filament\Tables\Columns\TextColumn::make('cor')->label('Cor'),
                \Filament\Tables\Columns\TextColumn::make('ordem')->label('Ordem')->sortable(),
                \Filament\Tables\Columns\IconColumn::make('ativo')->label('Ativo')->boolean(),
            ])
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
