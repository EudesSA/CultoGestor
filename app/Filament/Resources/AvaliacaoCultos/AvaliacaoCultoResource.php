<?php

namespace App\Filament\Resources\AvaliacaoCultos;

use App\Filament\Resources\AvaliacaoCultos\Pages\CreateAvaliacaoCulto;
use App\Filament\Resources\AvaliacaoCultos\Pages\EditAvaliacaoCulto;
use App\Filament\Resources\AvaliacaoCultos\Pages\ListAvaliacaoCultos;
use App\Filament\Resources\AvaliacaoCultos\Schemas\AvaliacaoCultoForm;
use App\Filament\Resources\AvaliacaoCultos\Tables\AvaliacaoCultosTable;
use App\Models\AvaliacaoCulto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AvaliacaoCultoResource extends Resource
{
    protected static ?string $model = AvaliacaoCulto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static UnitEnum|string|null $navigationGroup = 'Relatórios';

    protected static ?string $navigationLabel = 'Avaliações de Culto';

    protected static ?string $modelLabel = 'Avaliação';

    protected static ?string $pluralModelLabel = 'Avaliações';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return AvaliacaoCultoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AvaliacaoCultosTable::configure($table);
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
            'index' => ListAvaliacaoCultos::route('/'),
            'create' => CreateAvaliacaoCulto::route('/create'),
            'edit' => EditAvaliacaoCulto::route('/{record}/edit'),
        ];
    }
}
