<?php

namespace App\Filament\Resources\Anuncios\Schemas;

use App\Filament\Concerns\WithYoutubeField;
use App\Models\Culto;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class AnuncioForm
{
    use WithYoutubeField;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Link YouTube')
                    ->description('Opcional — para anúncios em vídeo do YouTube.')
                    ->collapsible()
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

                Section::make('Dados do Anúncio')
                    ->columns(2)
                    ->schema([
                        TextInput::make('titulo')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('tipo')
                            ->label('Tipo')
                            ->options([
                                'anuncio' => 'Anúncio',
                                'aviso' => 'Aviso',
                            ])
                            ->required(),

                        Select::make('categoria')
                            ->label('Categoria')
                            ->options([
                                'campal' => 'Campal',
                                'congresso' => 'Congresso',
                                'ja' => 'JA',
                                'desbravadores' => 'Desbravadores',
                                'aventureiros' => 'Aventureiros',
                                'mutirao' => 'Mutirão',
                                'santa_ceia' => 'Santa Ceia',
                                'batismo' => 'Batismo',
                                'geral' => 'Geral',
                            ])
                            ->default('geral')
                            ->required(),

                        Textarea::make('descricao')
                            ->label('Descrição')
                            ->rows(3)
                            ->columnSpanFull(),

                        DatePicker::make('data_inicio')
                            ->label('Data Início')
                            ->displayFormat('d/m/Y'),

                        DatePicker::make('data_fim')
                            ->label('Data Fim')
                            ->displayFormat('d/m/Y')
                            ->afterOrEqual('data_inicio'),

                        Toggle::make('ativo')
                            ->label('Ativo')
                            ->default(true),

                        Toggle::make('sempre_disponivel')
                            ->label('Sempre disponível no Modo Culto')
                            ->helperText('Se marcado, aparece no Modo Culto independente do período.'),

                        TextInput::make('ordem')
                            ->label('Ordem de exibição')
                            ->numeric()
                            ->default(0),

                        SpatieMediaLibraryFileUpload::make('thumbnail')
                            ->label('Thumbnail / Imagem de capa')
                            ->collection('thumbnail')
                            ->image()
                            ->imagePreviewHeight('120')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
