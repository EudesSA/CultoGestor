<?php

namespace App\Filament\Resources\Igrejas\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IgrejaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Identificação')
                ->columns(3)
                ->schema([
                    FileUpload::make('logo_path')
                        ->label('Logo')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('igrejas')
                        ->columnSpan(1),

                    \Filament\Schemas\Components\Grid::make(2)
                        ->columnSpan(2)
                        ->schema([
                            TextInput::make('nome')
                                ->label('Nome da Igreja')
                                ->required()
                                ->maxLength(150)
                                ->columnSpanFull(),

                            TextInput::make('sigla')
                                ->label('Sigla')
                                ->maxLength(20)
                                ->placeholder('Ex: IASD-Centro'),

                            TextInput::make('telefone')
                                ->label('Telefone')
                                ->tel()
                                ->maxLength(20),

                            TextInput::make('email')
                                ->label('E-mail')
                                ->email()
                                ->maxLength(150),

                            TextInput::make('site')
                                ->label('Site')
                                ->url()
                                ->maxLength(200),

                            Toggle::make('ativa')
                                ->label('Igreja ativa')
                                ->default(true),
                        ]),
                ]),

            Section::make('Endereço')
                ->columns(3)
                ->schema([
                    TextInput::make('cep')
                        ->label('CEP')
                        ->mask('99999-999')
                        ->maxLength(9)
                        ->columnSpan(1),

                    TextInput::make('endereco')
                        ->label('Logradouro')
                        ->maxLength(200)
                        ->columnSpan(2),

                    TextInput::make('numero')
                        ->label('Número')
                        ->maxLength(20),

                    TextInput::make('complemento')
                        ->label('Complemento')
                        ->maxLength(100),

                    TextInput::make('bairro')
                        ->label('Bairro')
                        ->maxLength(100),

                    TextInput::make('cidade')
                        ->label('Cidade')
                        ->maxLength(100),

                    TextInput::make('estado')
                        ->label('Estado (UF)')
                        ->maxLength(2)
                        ->placeholder('SP'),
                ]),

            Section::make('Geolocalização')
                ->description('Coordenadas para exibição no Google Maps. Deixe em branco para gerar a busca pelo endereço.')
                ->columns(2)
                ->collapsed()
                ->schema([
                    TextInput::make('latitude')
                        ->label('Latitude')
                        ->numeric()
                        ->placeholder('-23.5505')
                        ->helperText('Ex: -23.5505 (negativo para Sul)'),

                    TextInput::make('longitude')
                        ->label('Longitude')
                        ->numeric()
                        ->placeholder('-46.6333')
                        ->helperText('Ex: -46.6333 (negativo para Oeste)'),
                ]),
        ]);
    }
}
