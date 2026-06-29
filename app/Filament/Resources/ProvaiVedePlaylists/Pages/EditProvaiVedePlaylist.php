<?php

namespace App\Filament\Resources\ProvaiVedePlaylists\Pages;

use App\Filament\Resources\ProvaiVedePlaylists\ProvaiVedePlaylistResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProvaiVedePlaylist extends EditRecord
{
    protected static string $resource = ProvaiVedePlaylistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
