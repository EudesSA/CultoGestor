<?php

namespace App\Filament\Resources\Funcaos\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FuncaoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados da Função')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nome')
                            ->label('Nome')
                            ->required()
                            ->maxLength(100),

                        ColorPicker::make('cor')
                            ->label('Cor')
                            ->default('#6B7280'),

                        TextInput::make('icone')
                            ->label('Ícone Heroicon')
                            ->placeholder('heroicon-o-speaker-wave'),

                        TextInput::make('ordem')
                            ->label('Ordem')
                            ->numeric()
                            ->default(0),

                        Toggle::make('ativo')
                            ->label('Ativo')
                            ->default(true),
                    ]),
            ]);
    }
}
