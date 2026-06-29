<?php

namespace App\Filament\Resources\Cultos\RelationManagers;

use App\Models\Anuncio;
use App\Models\Hino;
use App\Models\Informativo;
use App\Models\Musica;
use App\Models\ProvaiVede;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LiturgiaRelationManager extends RelationManager
{
    protected static string $relationship = 'liturgias';

    protected static ?string $title = 'Liturgia / Ordem do Culto';

    private const TIPOS = [
        'hino'        => 'Hino',
        'musica'      => 'Música Especial',
        'video'       => 'Provai e Vede',
        'informativo' => 'Informativo',
        'anuncio'     => 'Anúncio',
        'oracao'      => 'Oração',
        'sermao'      => 'Sermão / Mensagem',
        'ofertorio'   => 'Ofertório',
        'item_livre'  => 'Item livre',
    ];

    /** Model polimórfico de referência por tipo (null = item sem vínculo). */
    private static function modeloPara(?string $tipo): ?string
    {
        return match ($tipo) {
            'hino'        => Hino::class,
            'musica'      => Musica::class,
            'video'       => ProvaiVede::class,
            'informativo' => Informativo::class,
            'anuncio'     => Anuncio::class,
            default       => null,
        };
    }

    private static function opcoesReferencia(?string $tipo): array
    {
        return match ($tipo) {
            'hino' => Hino::orderBy('numero')->get()
                ->mapWithKeys(fn ($h) => [$h->id => "#{$h->numero} — {$h->titulo}"])->toArray(),
            'musica' => Musica::with('cantor')->latest('id')->limit(200)->get()
                ->mapWithKeys(fn ($m) => [$m->id => $m->nome . ($m->cantor ? " — {$m->cantor->nome}" : '')])->toArray(),
            'video' => ProvaiVede::orderByDesc('id')->limit(200)->pluck('titulo', 'id')->toArray(),
            'informativo' => Informativo::orderByDesc('id')->limit(200)->pluck('titulo', 'id')->toArray(),
            'anuncio' => Anuncio::orderByDesc('id')->limit(200)->pluck('titulo', 'id')->toArray(),
            default => [],
        };
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('tipo')
                ->label('Tipo')
                ->options(self::TIPOS)
                ->required()
                ->live()
                ->afterStateUpdated(function (Set $set, $state) {
                    $set('referencia_id', null);
                    // Define o tipo polimórfico conforme o tipo escolhido.
                    $set('referencia_type', self::modeloPara($state));
                })
                ->columnSpan(1),

            // Guarda o model polimórfico (Hino/Musica/ProvaiVede/...) — preenchido pelo tipo.
            Hidden::make('referencia_type'),

            TextInput::make('titulo')
                ->label('Título')
                ->required()
                ->maxLength(255)
                ->columnSpan(1),

            Select::make('referencia_id')
                ->label('Item vinculado')
                ->helperText('Opcional — vincula a música/vídeo/anúncio/hino para abrir no Modo Culto.')
                ->options(fn (Get $get) => self::opcoesReferencia($get('tipo')))
                ->searchable()
                ->visible(fn (Get $get): bool => self::modeloPara($get('tipo')) !== null)
                ->live()
                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                    // Preenche o título com o nome do item escolhido, se vazio.
                    if ($state && ! $get('titulo')) {
                        $opcoes = self::opcoesReferencia($get('tipo'));
                        $set('titulo', $opcoes[$state] ?? '');
                    }
                })
                ->columnSpanFull(),

            TimePicker::make('horario_previsto')
                ->label('Horário previsto')
                ->seconds(false),

            TextInput::make('duracao_minutos')
                ->label('Duração (min)')
                ->numeric()
                ->minValue(0),

            Textarea::make('observacao')
                ->label('Observação')
                ->rows(2)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo')
            ->defaultSort('ordem')
            ->reorderable('ordem')
            ->columns([
                TextColumn::make('ordem')->label('#')->sortable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::TIPOS[$state] ?? ucfirst($state)),

                TextColumn::make('titulo')->label('Título')->searchable()->wrap(),

                TextColumn::make('horario_previsto')
                    ->label('Horário')
                    ->formatStateUsing(fn ($state): string => $state ? substr($state, 0, 5) : '—'),

                TextColumn::make('duracao_minutos')
                    ->label('Min')
                    ->placeholder('—'),

                TextColumn::make('referencia_id')
                    ->label('Vinculado')
                    ->formatStateUsing(fn ($state): string => $state ? '✓' : '—')
                    ->alignCenter(),
            ])
            ->headerActions([
                CreateAction::make()->label('Adicionar item'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
