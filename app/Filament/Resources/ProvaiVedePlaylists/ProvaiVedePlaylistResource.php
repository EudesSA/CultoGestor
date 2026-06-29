<?php

namespace App\Filament\Resources\ProvaiVedePlaylists;

use App\Filament\Resources\ProvaiVedePlaylists\Pages\CreateProvaiVedePlaylist;
use App\Filament\Resources\ProvaiVedePlaylists\Pages\EditProvaiVedePlaylist;
use App\Filament\Resources\ProvaiVedePlaylists\Pages\ListProvaiVedePlaylists;
use App\Filament\Resources\ProvaiVedePlaylists\Schemas\ProvaiVedePlaylistForm;
use App\Filament\Resources\ProvaiVedePlaylists\Tables\ProvaiVedePlaylistsTable;
use App\Models\ProvaiVedePlaylist;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProvaiVedePlaylistResource extends Resource
{
    protected static ?string $model = ProvaiVedePlaylist::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static UnitEnum|string|null $navigationGroup = 'Configurações';

    protected static ?string $navigationLabel = 'Playlists Provai e Vede';

    protected static ?string $modelLabel = 'Playlist Provai e Vede';

    protected static ?string $pluralModelLabel = 'Playlists Provai e Vede';

    protected static ?string $recordTitleAttribute = 'nome';

    public static function form(Schema $schema): Schema
    {
        return ProvaiVedePlaylistForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProvaiVedePlaylistsTable::configure($table);
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
            'index' => ListProvaiVedePlaylists::route('/'),
            'create' => CreateProvaiVedePlaylist::route('/create'),
            'edit' => EditProvaiVedePlaylist::route('/{record}/edit'),
        ];
    }
}
