<?php

namespace App\Filament\Resources\Hinos;

use App\Filament\Resources\Hinos\Pages\CreateHino;
use App\Filament\Resources\Hinos\Pages\EditHino;
use App\Filament\Resources\Hinos\Pages\ListHinos;
use App\Filament\Resources\Hinos\Schemas\HinoForm;
use App\Filament\Resources\Hinos\Tables\HinosTable;
use App\Models\Hino;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HinoResource extends Resource
{
    protected static ?string $model = Hino::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static UnitEnum|string|null $navigationGroup = 'Músicas & Cantores';

    protected static ?string $navigationLabel = 'Banco de Hinos';

    protected static ?string $modelLabel = 'Hino';

    protected static ?string $pluralModelLabel = 'Hinos';

    protected static ?string $recordTitleAttribute = 'titulo';

    public static function form(Schema $schema): Schema
    {
        return HinoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HinosTable::configure($table);
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
            'index' => ListHinos::route('/'),
            'create' => CreateHino::route('/create'),
            'edit' => EditHino::route('/{record}/edit'),
        ];
    }
}
