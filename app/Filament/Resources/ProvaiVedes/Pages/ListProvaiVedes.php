<?php

namespace App\Filament\Resources\ProvaiVedes\Pages;

use App\Filament\Resources\ProvaiVedes\ProvaiVedeResource;
use App\Jobs\MonitorarProvaiVedeJob;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListProvaiVedes extends ListRecords
{
    protected static string $resource = ProvaiVedeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('buscar_videos')
                ->label('Buscar novos vídeos')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Busca novos vídeos na página do Provai e Vede e cria registros pendentes de aprovação.')
                ->action(function () {
                    $novos = app(MonitorarProvaiVedeJob::class)->handle(app(\App\Services\YoutubeService::class));

                    Notification::make()
                        ->title($novos > 0 ? "{$novos} novo(s) vídeo(s) importado(s)" : 'Nenhum vídeo novo encontrado')
                        ->body($novos > 0 ? 'Revise e aprove na lista (filtro “Pendentes de aprovação”).' : null)
                        ->success()
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}
