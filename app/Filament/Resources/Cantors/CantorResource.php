<?php

namespace App\Filament\Resources\Cantors;

use App\Filament\Resources\Cantors\Pages\CreateCantor;
use App\Filament\Resources\Cantors\Pages\EditCantor;
use App\Filament\Resources\Cantors\Pages\ListCantors;
use App\Filament\Resources\Cantors\Schemas\CantorForm;
use App\Filament\Resources\Cantors\RelationManagers\MusicasRelationManager;
use App\Filament\Resources\Cantors\Tables\CantorsTable;
use App\Models\Cantor;
use Filament\Resources\Resource;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CantorResource extends Resource
{
    protected static ?string $model = Cantor::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-microphone';
    protected static UnitEnum|string|null $navigationGroup = 'Músicas & Cantores';
    protected static ?string $navigationLabel = 'Cantores';
    protected static ?string $modelLabel = 'Cantor';
    protected static ?string $pluralModelLabel = 'Cantores';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CantorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CantorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            MusicasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCantors::route('/'),
            'create' => CreateCantor::route('/create'),
            'edit' => EditCantor::route('/{record}/edit'),
        ];
    }
}

