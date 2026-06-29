<?php

namespace App\Filament\Resources\Hinos\Schemas;

use App\Filament\Concerns\WithYoutubeField;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HinoForm
{
    use WithYoutubeField;

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Hino')
                ->columns(2)
                ->schema([
                    TextInput::make('numero')
                        ->label('Número')
                        ->required()
                        ->numeric()
                        ->minValue(1),

                    TextInput::make('hinario')
                        ->label('Hinário')
                        ->required()
                        ->default('Hinário Adventista'),

                    TextInput::make('titulo')
                        ->label('Título')
                        ->required()
                        ->columnSpanFull(),

                    TextInput::make('tom')
                        ->label('Tom padrão')
                        ->maxLength(10)
                        ->placeholder('Ex: C, D, Sol'),

                    TextInput::make('tons_alternativos')
                        ->label('Tons alternativos')
                        ->placeholder('Ex: C, D, E'),

                    static::youtubeInput([
                        'titulo' => 'titulo',
                    ]),

                    Textarea::make('observacoes')
                        ->label('Observações')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
