<?php

namespace App\Filament\Resources\Anuncios;

use App\Filament\Resources\Anuncios\Pages\CreateAnuncio;
use App\Filament\Resources\Anuncios\Pages\EditAnuncio;
use App\Filament\Resources\Anuncios\Pages\ListAnuncios;
use App\Filament\Resources\Anuncios\Schemas\AnuncioForm;
use App\Filament\Resources\Anuncios\RelationManagers\MidiasRelationManager;
use App\Filament\Resources\Anuncios\Tables\AnunciosTable;
use App\Models\Anuncio;
use Filament\Resources\Resource;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AnuncioResource extends Resource
{
    protected static ?string $model = Anuncio::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';
    protected static UnitEnum|string|null $navigationGroup = 'Bibliotecas';
    protected static ?string $navigationLabel = 'Anúncios';
    protected static ?string $modelLabel = 'Anúncio';
    protected static ?string $pluralModelLabel = 'Anúncios';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return AnuncioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnunciosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            MidiasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnuncios::route('/'),
            'create' => CreateAnuncio::route('/create'),
            'edit' => EditAnuncio::route('/{record}/edit'),
        ];
    }
}

