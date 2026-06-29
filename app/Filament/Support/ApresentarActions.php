<?php

namespace App\Filament\Support;

use Filament\Actions\Action;

/**
 * Ações reutilizáveis de tabela para models que usam o trait Apresentavel:
 *  - "Abrir": abre o link do YouTube ou o arquivo em nova aba.
 *  - "Apresentar": abre a página de apresentação em tela cheia (com seletor
 *    de monitor) em nova janela.
 */
class ApresentarActions
{
    /** @return array<Action> */
    public static function make(): array
    {
        return [
            Action::make('abrir')
                ->label('Abrir')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn ($record) => $record->urlAbrir())
                ->openUrlInNewTab()
                ->visible(fn ($record) => filled($record->urlAbrir())),

            Action::make('apresentar')
                ->label('Apresentar')
                ->icon('heroicon-o-tv')
                ->color('info')
                ->url(fn ($record) => $record->urlApresentacao())
                ->openUrlInNewTab()
                ->visible(fn ($record) => $record->temApresentacao()),
        ];
    }
}
