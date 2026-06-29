<?php

namespace App\Filament\Resources\CultoTipos;

use App\Filament\Resources\CultoTipos\Pages\CreateCultoTipo;
use App\Filament\Resources\CultoTipos\Pages\EditCultoTipo;
use App\Filament\Resources\CultoTipos\Pages\ListCultoTipos;
use App\Filament\Resources\CultoTipos\Schemas\CultoTipoForm;
use App\Filament\Resources\CultoTipos\Tables\CultoTiposTable;
use App\Models\CultoTipo;
use Filament\Resources\Resource;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CultoTipoResource extends Resource
{
    protected static ?string $model = CultoTipo::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    protected static UnitEnum|string|null $navigationGroup = 'Configurações';
    protected static ?string $navigationLabel = 'Tipos de Culto';
    protected static ?string $modelLabel = 'Tipo de Culto';
    protected static ?string $pluralModelLabel = 'Tipos de Culto';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return CultoTipoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CultoTiposTable::configure($table);
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
            'index' => ListCultoTipos::route('/'),
            'create' => CreateCultoTipo::route('/create'),
            'edit' => EditCultoTipo::route('/{record}/edit'),
        ];
    }
}

