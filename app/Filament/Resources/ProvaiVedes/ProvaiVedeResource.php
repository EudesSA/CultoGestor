<?php

namespace App\Filament\Resources\ProvaiVedes;

use App\Filament\Resources\ProvaiVedes\Pages\CreateProvaiVede;
use App\Filament\Resources\ProvaiVedes\Pages\EditProvaiVede;
use App\Filament\Resources\ProvaiVedes\Pages\ListProvaiVedes;
use App\Filament\Resources\ProvaiVedes\Schemas\ProvaiVedeForm;
use App\Filament\Resources\ProvaiVedes\Tables\ProvaiVedesTable;
use App\Models\ProvaiVede;
use Filament\Resources\Resource;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProvaiVedeResource extends Resource
{
    protected static ?string $model = ProvaiVede::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play-circle';
    protected static UnitEnum|string|null $navigationGroup = 'Bibliotecas';
    protected static ?string $navigationLabel = 'Provai e Vede';
    protected static ?string $modelLabel = 'Vídeo';
    protected static ?string $pluralModelLabel = 'Provai e Vede';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ProvaiVedeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProvaiVedesTable::configure($table);
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
            'index' => ListProvaiVedes::route('/'),
            'create' => CreateProvaiVede::route('/create'),
            'edit' => EditProvaiVede::route('/{record}/edit'),
        ];
    }
}

