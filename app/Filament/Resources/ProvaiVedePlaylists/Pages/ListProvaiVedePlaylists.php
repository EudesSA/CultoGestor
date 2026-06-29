<?php

namespace App\Filament\Resources\ProvaiVedePlaylists\Pages;

use App\Filament\Resources\ProvaiVedePlaylists\ProvaiVedePlaylistResource;
use App\Jobs\MonitorarProvaiVedeJob;
use App\Services\YoutubeService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListProvaiVedePlaylists extends ListRecords
{
    protected static string $resource = ProvaiVedePlaylistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('buscar_videos')
                ->label('Buscar vídeos agora')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->requiresConfirmation()
                ->modalDescription('Busca nas playlists ativas os vídeos novos (data ≥ hoje, exceto Libras) e cria registros pendentes de aprovação em Provai e Vede.')
                ->action(function () {
                    $novos = app(MonitorarProvaiVedeJob::class)->handle(app(YoutubeService::class));

                    Notification::make()
                        ->title($novos > 0 ? "{$novos} novo(s) vídeo(s) importado(s)" : 'Nenhum vídeo novo encontrado')
                        ->body($novos > 0 ? 'Revise e aprove em Provai e Vede (filtro “Pendentes de aprovação”).' : null)
                        ->success()
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}
