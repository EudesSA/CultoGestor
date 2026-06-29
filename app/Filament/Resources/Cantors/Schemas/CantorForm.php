<?php

namespace App\Filament\Resources\Cantors\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CantorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Dados do Cantor')
                ->columns(3)
                ->schema([
                    FileUpload::make('foto')
                        ->label('Foto')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('cantores')
                        ->avatar()
                        ->circleCropper()
                        ->helperText('Opcional para cantores com conta no sistema.')
                        ->columnSpan(1),

                    \Filament\Schemas\Components\Grid::make(2)
                        ->columnSpan(2)
                        ->schema([
                            TextInput::make('nome')
                                ->label('Nome')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            TextInput::make('email')
                                ->label('E-mail')
                                ->email()
                                ->maxLength(255),

                            TextInput::make('telefone')
                                ->label('WhatsApp')
                                ->tel()
                                ->mask('(99) 99999-9999')
                                ->maxLength(20),

                            Select::make('voz')
                                ->label('Voz')
                                ->options([
                                    'soprano'   => 'Soprano',
                                    'contralto' => 'Contralto',
                                    'tenor'     => 'Tenor',
                                    'baritono'  => 'Barítono',
                                    'baixo'     => 'Baixo',
                                    'outro'     => 'Outro',
                                ])
                                ->searchable(),

                            Toggle::make('ativo')
                                ->label('Ativo')
                                ->default(true),

                            Textarea::make('observacoes')
                                ->label('Observações')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                ]),
        ]);
    }
}
