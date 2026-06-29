<?php

namespace App\Filament\Concerns;

use App\Services\YoutubeService;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;

trait WithYoutubeField
{
    /**
     * Retorna um TextInput de link_youtube que, ao perder o foco,
     * busca automaticamente metadados via oEmbed e preenche os campos indicados.
     *
     * $camposMapeados: ['campo_form' => 'chave_retorno_service']
     * Chaves disponíveis: titulo, canal, thumbnail, duracao_segundos
     */
    public static function youtubeInput(array $camposMapeados = []): TextInput
    {
        return TextInput::make('link_youtube')
            ->label('Link YouTube')
            ->url()
            ->suffixIcon('heroicon-o-play-circle')
            ->live(onBlur: true)
            ->afterStateUpdated(function (?string $state, Set $set) use ($camposMapeados) {
                if (! $state) {
                    return;
                }

                $dados = app(YoutubeService::class)->buscarMetadados($state);
                if (! $dados) {
                    return;
                }

                foreach ($camposMapeados as $campoBlade => $chaveRetorno) {
                    if (isset($dados[$chaveRetorno]) && $dados[$chaveRetorno] !== null) {
                        $set($campoBlade, $dados[$chaveRetorno]);
                    }
                }
            })
            ->helperText('Cole o link e o sistema buscará título, canal e duração automaticamente.')
            ->columnSpanFull();
    }
}
