<?php

namespace App\Filament\Resources\Ensaios;

use App\Filament\Resources\Ensaios\Pages\CreateEnsaio;
use App\Filament\Resources\Ensaios\Pages\EditEnsaio;
use App\Filament\Resources\Ensaios\Pages\ListEnsaios;
use App\Filament\Resources\Ensaios\Schemas\EnsaioForm;
use App\Filament\Resources\Ensaios\Tables\EnsaiosTable;
use App\Filament\Resources\Ensaios\RelationManagers\MusicasRelationManager;
use App\Filament\Resources\Ensaios\RelationManagers\ParticipantesRelationManager;
use App\Models\Ensaio;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EnsaioResource extends Resource
{
    protected static ?string $model = Ensaio::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMusicalNote;

    protected static UnitEnum|string|null $navigationGroup = 'Músicas & Cantores';

    protected static ?string $navigationLabel = 'Ensaios';

    protected static ?string $modelLabel = 'Ensaio';

    protected static ?string $pluralModelLabel = 'Ensaios';

    public static function form(Schema $schema): Schema
    {
        return EnsaioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EnsaiosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ParticipantesRelationManager::class,
            MusicasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEnsaios::route('/'),
            'create' => CreateEnsaio::route('/create'),
            'edit' => EditEnsaio::route('/{record}/edit'),
        ];
    }
}
