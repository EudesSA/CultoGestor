<?php

namespace App\Filament\Resources\Musicas\Schemas;

use App\Filament\Concerns\WithYoutubeField;
use App\Filament\Resources\Musicas\MusicaResource;
use App\Models\Culto;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MusicaForm
{
    use WithYoutubeField;

    public static function configure(Schema $schema): Schema
    {
        $cantorRestrito = MusicaResource::cantorRestrito();
        $podeGerenciar  = MusicaResource::podeGerenciarMusicas();
        $cantorIdProprio = auth()->user()?->cantor?->id;

        return $schema->components([

            Section::make('Dados da Música')
                ->columns(2)
                ->schema([
                    Select::make('culto_id')
                        ->label('Culto')
                        ->helperText('Opcional — deixe em branco para repertório; associe quando o cantor for escalado.')
                        ->placeholder('Sem culto (repertório)')
                        ->options(
                            fn () => Culto::with('tipo')
                                ->where('data', '>=', now()->subMonths(1))
                                ->orderBy('data', 'desc')
                                ->get()
                                ->mapWithKeys(fn ($c) => [
                                    $c->id => $c->tipo?->nome . ' — ' . $c->data?->format('d/m/Y'),
                                ])
                        )
                        ->searchable()
                        ->nullable()
                        ->columnSpanFull(),

                    Select::make('cantor_id')
                        ->label('Cantor')
                        ->relationship('cantor', 'nome')
                        ->searchable()
                        ->preload()
                        ->required()
                        // Cantor logado: vem com ele mesmo selecionado e não pode trocar.
                        ->default($cantorRestrito ? $cantorIdProprio : null)
                        ->disabled($cantorRestrito)
                        ->dehydrated()
                        ->helperText($cantorRestrito ? 'Suas músicas são vinculadas automaticamente ao seu nome.' : null),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'pendente'  => 'Pendente',
                            'enviado'   => 'Enviado',
                            'revisado'  => 'Revisado',
                            'aprovado'  => 'Aprovado',
                        ])
                        ->default('pendente')
                        ->required($podeGerenciar)
                        // Só a gestão (Diretor/Sonoplasta/Administrador) aprova/reprova.
                        ->disabled($cantorRestrito)
                        ->dehydrated($podeGerenciar)
                        ->helperText($cantorRestrito ? 'O status é definido pela equipe ao revisar a música.' : null),
                ]),

            Section::make('Link YouTube')
                ->description('Cole o link para preencher automaticamente título, canal e duração.')
                ->schema([
                    static::youtubeInput([
                        'nome'            => 'titulo',
                        'artista'         => 'canal',
                        'duracao_segundos' => 'duracao_segundos',
                        'youtube_titulo'   => 'titulo',
                        'youtube_canal'    => 'canal',
                        'youtube_thumbnail' => 'thumbnail',
                    ]),

                    // Prévia do thumbnail quando preenchido
                    Placeholder::make('youtube_thumbnail_preview')
                        ->label('Thumbnail')
                        ->content(fn ($record) => $record?->youtube_thumbnail
                            ? '<img src="' . $record->youtube_thumbnail . '" class="rounded-lg max-h-24" />'
                            : null
                        )
                        ->visible(fn ($record) => (bool) $record?->youtube_thumbnail),
                ]),

            Section::make('Arquivo (upload)')
                ->description('Opcional — vídeo, imagem, PDF ou áudio para apresentar sem depender do YouTube.')
                ->collapsible()
                ->schema([
                    SpatieMediaLibraryFileUpload::make('arquivo')
                        ->label('Arquivo')
                        ->collection('arquivo')
                        ->disk('cultos')
                        ->acceptedFileTypes([
                            'video/mp4', 'video/quicktime', 'video/webm',
                            'image/jpeg', 'image/png', 'image/webp', 'image/gif',
                            'application/pdf',
                            'audio/mpeg', 'audio/wav', 'audio/ogg',
                        ])
                        ->maxSize(204800)
                        ->columnSpanFull(),
                ]),

            Section::make('Informações da Música')
                ->columns(2)
                ->schema([
                    TextInput::make('nome')
                        ->label('Nome da Música')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('artista')
                        ->label('Artista / Cantor original')
                        ->maxLength(255),

                    TextInput::make('tom')
                        ->label('Tom')
                        ->maxLength(10)
                        ->placeholder('Ex: Dó, Sol, Lá♭'),

                    TextInput::make('duracao_segundos')
                        ->label('Duração (segundos)')
                        ->numeric()
                        ->minValue(0)
                        ->helperText('Preenchido automaticamente via YouTube se disponível.'),

                    Textarea::make('observacoes_diretor')
                        ->label('Observações do Diretor')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
