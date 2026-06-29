<?php

namespace App\Filament\Resources\ProvaiVedePlaylists\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProvaiVedePlaylistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Playlist do Provai e Vede')
                ->description('Cole a URL da playlist do YouTube (ou só o ID PL…). O sistema busca os vídeos novos a partir dela.')
                ->schema([
                    TextInput::make('nome')
                        ->label('Nome')
                        ->placeholder('Ex: Provai e Vede - Julho 2026')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('playlist_id')
                        ->label('URL ou ID da playlist')
                        ->placeholder('https://www.youtube.com/playlist?list=PL… ou PL…')
                        ->helperText('Pode colar o link inteiro da playlist; o sistema extrai o ID automaticamente.')
                        ->required()
                        ->maxLength(255),

                    Toggle::make('ativo')
                        ->label('Ativa (incluir na busca)')
                        ->default(true),
                ]),
        ]);
    }
}
