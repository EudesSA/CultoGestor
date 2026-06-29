<?php

namespace App\Filament\Resources\Escalas;

use App\Filament\Resources\Escalas\Pages\CreateEscala;
use App\Filament\Resources\Escalas\Pages\EditEscala;
use App\Filament\Resources\Escalas\Pages\ListEscalas;
use App\Filament\Resources\Escalas\Schemas\EscalaForm;
use App\Filament\Resources\Escalas\Tables\EscalasTable;
use App\Models\Escala;
use Filament\Resources\Resource;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class EscalaResource extends Resource
{
    protected static ?string $model = Escala::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static UnitEnum|string|null $navigationGroup = 'Programação';
    protected static ?string $navigationLabel = 'Escalas';
    protected static ?string $modelLabel = 'Escala';
    protected static ?string $pluralModelLabel = 'Escalas';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return EscalaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EscalasTable::configure($table);
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
            'index' => ListEscalas::route('/'),
            'create' => CreateEscala::route('/create'),
            'edit' => EditEscala::route('/{record}/edit'),
        ];
    }
}

