<?php

namespace App\Filament\Resources\Cultos\Pages;

use App\Filament\Resources\Cultos\CultoResource;
use App\Jobs\GerarLiturgiaJaJob;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditCulto extends EditRecord
{
    protected static string $resource = CultoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('modoCulto')
                ->label('Modo Culto')
                ->icon('heroicon-o-tv')
                ->color('gray')
                ->url(fn () => route('culto.modo', $this->record->id))
                ->openUrlInNewTab(),

            Action::make('gerarLiturgiaJa')
                ->label('Gerar Liturgia JA')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Gerar arquivo Louvor JA')
                ->modalDescription('Gera um arquivo .ja com a liturgia deste culto para importar no software Louvor JA.')
                ->modalSubmitActionLabel('Gerar e baixar')
                ->action(function () {
                    $exportacao = GerarLiturgiaJaJob::gerar($this->record->id, Auth::id());

                    Notification::make()
                        ->title('Arquivo .ja gerado com sucesso!')
                        ->success()
                        ->actions([
                            Action::make('download')
                                ->label('Baixar arquivo')
                                ->url(route('liturgia.download', $exportacao->id))
                                ->openUrlInNewTab(),
                        ])
                        ->send();
                }),

            DeleteAction::make(),
        ];
    }
}
