<?php

namespace App\Filament\Resources\ProvaiVedes\Schemas;

use App\Filament\Concerns\WithYoutubeField;
use App\Models\Culto;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ProvaiVedeForm
{
    use WithYoutubeField;

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Link YouTube')
                ->description('Cole o link para preencher título, canal e duração automaticamente.')
                ->schema([
                    static::youtubeInput([
                        'titulo'            => 'titulo',
                        'youtube_canal'     => 'canal',
                        'youtube_thumbnail' => 'thumbnail',
                        'duracao_segundos'  => 'duracao_segundos',
                    ]),

                    Placeholder::make('thumb_preview')
                        ->label('Prévia do thumbnail')
                        ->content(fn (Get $get): ?HtmlString => $get('youtube_thumbnail')
                            ? new HtmlString('<img src="' . e($get('youtube_thumbnail')) . '" class="rounded-lg max-h-40">')
                            : null)
                        ->visible(fn (Get $get): bool => (bool) $get('youtube_thumbnail')),
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

            Section::make('Programação')
                ->schema([
                    Select::make('culto_id')
                        ->label('Culto em que será apresentado')
                        ->placeholder('Selecione o culto...')
                        ->options(
                            fn () => Culto::with('tipo')
                                ->where('data', '>=', now()->subDays(7))
                                ->orderBy('data')
                                ->get()
                                ->mapWithKeys(fn ($c) => [
                                    $c->id => ($c->tipo?->nome ?? 'Culto') . ' — ' . $c->data?->format('d/m/Y'),
                                ])
                        )
                        ->searchable()
                        ->nullable()
                        ->columnSpanFull(),
                ]),

            Section::make('Dados do Vídeo')
                ->columns(2)
                ->schema([
                    TextInput::make('titulo')
                        ->label('Título')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    TextInput::make('youtube_canal')
                        ->label('Canal YouTube')
                        ->maxLength(255)
                        ->readOnly()
                        ->helperText('Preenchido automaticamente.'),

                    TextInput::make('tema')
                        ->label('Tema')
                        ->maxLength(255),

                    Select::make('categoria')
                        ->label('Categoria')
                        ->options([
                            'saude'           => 'Saúde',
                            'familia'         => 'Família',
                            'alimentacao'     => 'Alimentação',
                            'espiritualidade' => 'Espiritualidade',
                            'educacao'        => 'Educação',
                            'relacionamentos' => 'Relacionamentos',
                            'financas'        => 'Finanças',
                            'outro'           => 'Outro',
                        ])
                        ->required(),

                    DatePicker::make('data_exibicao')
                        ->label('Data de Exibição')
                        ->displayFormat('d/m/Y'),

                    TextInput::make('duracao_segundos')
                        ->label('Duração (segundos)')
                        ->numeric()
                        ->helperText('Preenchido automaticamente via YouTube API se configurada.'),

                    TextInput::make('trimestre')
                        ->label('Trimestre')
                        ->placeholder('Ex: 2026/2T'),

                    Select::make('status_importacao')
                        ->label('Status')
                        ->options([
                            'pendente_aprovacao' => 'Pendente Aprovação',
                            'ativo'              => 'Ativo',
                            'inativo'            => 'Inativo',
                        ])
                        ->default('ativo'),

                    Toggle::make('ativo')
                        ->label('Ativo')
                        ->default(true),
                ]),
        ]);
    }
}
