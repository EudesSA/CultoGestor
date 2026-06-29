<?php

namespace App\Filament\Resources\Informativos;

use App\Filament\Resources\Informativos\Pages\CreateInformativo;
use App\Filament\Resources\Informativos\Pages\EditInformativo;
use App\Filament\Resources\Informativos\Pages\ListInformativos;
use App\Filament\Resources\Informativos\Schemas\InformativoForm;
use App\Filament\Resources\Informativos\Tables\InformativosTable;
use App\Models\Informativo;
use Filament\Resources\Resource;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class InformativoResource extends Resource
{
    protected static ?string $model = Informativo::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';
    protected static UnitEnum|string|null $navigationGroup = 'Bibliotecas';
    protected static ?string $navigationLabel = 'Inf. Missões';
    protected static ?string $modelLabel = 'Informativo';
    protected static ?string $pluralModelLabel = 'Informativo Mundial das Missões';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return InformativoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InformativosTable::configure($table);
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
            'index' => ListInformativos::route('/'),
            'create' => CreateInformativo::route('/create'),
            'edit' => EditInformativo::route('/{record}/edit'),
        ];
    }
}

