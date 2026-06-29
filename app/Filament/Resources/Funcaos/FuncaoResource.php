<?php

namespace App\Filament\Resources\Funcaos;

use App\Filament\Resources\Funcaos\Pages\CreateFuncao;
use App\Filament\Resources\Funcaos\Pages\EditFuncao;
use App\Filament\Resources\Funcaos\Pages\ListFuncaos;
use App\Filament\Resources\Funcaos\Schemas\FuncaoForm;
use App\Filament\Resources\Funcaos\Tables\FuncaosTable;
use App\Models\Funcao;
use Filament\Resources\Resource;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FuncaoResource extends Resource
{
    protected static ?string $model = Funcao::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';
    protected static UnitEnum|string|null $navigationGroup = 'Configurações';
    protected static ?string $navigationLabel = 'Funções';
    protected static ?string $modelLabel = 'Função';
    protected static ?string $pluralModelLabel = 'Funções';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return FuncaoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FuncaosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFuncaos::route('/'),
            'create' => CreateFuncao::route('/create'),
            'edit' => EditFuncao::route('/{record}/edit'),
        ];
    }
}

