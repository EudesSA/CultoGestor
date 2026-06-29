<?php

namespace App\Filament\Resources\Igrejas;

use App\Filament\Resources\Igrejas\Pages\CreateIgreja;
use App\Filament\Resources\Igrejas\Pages\EditIgreja;
use App\Filament\Resources\Igrejas\Pages\ListIgrejas;
use App\Filament\Resources\Igrejas\Schemas\IgrejaForm;
use App\Filament\Resources\Igrejas\Tables\IgrejasTable;
use App\Models\Igreja;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class IgrejaResource extends Resource
{
    protected static ?string $model = Igreja::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-library';
    protected static UnitEnum|string|null $navigationGroup = 'Configurações';
    protected static ?string $navigationLabel = 'Igrejas';
    protected static ?string $modelLabel = 'Igreja';
    protected static ?string $pluralModelLabel = 'Igrejas';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return IgrejaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IgrejasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListIgrejas::route('/'),
            'create' => CreateIgreja::route('/create'),
            'edit'   => EditIgreja::route('/{record}/edit'),
        ];
    }
}
