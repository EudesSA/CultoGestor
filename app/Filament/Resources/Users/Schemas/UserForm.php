<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Funcao;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Foto e Identificação')
                ->columns(3)
                ->schema([
                    FileUpload::make('avatar')
                        ->label('Foto')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('avatars')
                        ->avatar()
                        ->circleCropper()
                        ->columnSpan(1),

                    \Filament\Schemas\Components\Grid::make(2)
                        ->columnSpan(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Nome completo')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            TextInput::make('email')
                                ->label('E-mail')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),

                            TextInput::make('phone_whatsapp')
                                ->label('WhatsApp')
                                ->tel()
                                ->mask('(99) 99999-9999')
                                ->maxLength(20),

                            DatePicker::make('data_nascimento')
                                ->label('Data de Nascimento')
                                ->displayFormat('d/m/Y')
                                ->maxDate(now()),

                            Select::make('genero')
                                ->label('Gênero')
                                ->options([
                                    'masculino' => 'Masculino',
                                    'feminino' => 'Feminino',
                                    'outro' => 'Outro',
                                ]),

                            TextInput::make('password')
                                ->label('Senha')
                                ->password()
                                ->revealable()
                                ->required(fn(string $operation) => $operation === 'create')
                                ->dehydrated(fn(?string $state) => filled($state))
                                ->dehydrateStateUsing(fn(string $state) => bcrypt($state))
                                ->helperText('Deixe em branco para manter a senha atual.')
                                ->columnSpanFull(),

                            Select::make('roles')
                                ->label('Função / Permissão Marque as funções que este membro exerce no SISTEMA.')
                                ->multiple()
                                ->options(Role::pluck('name', 'name'))
                                ->preload()
                                ->relationship('roles', 'name')
                                ->live()
                                ->columnSpanFull(),
                        ]),
                ]),

            Section::make('Funções no culto')
                ->description('Marque as funções que este membro exerce. Ao montar a escala, cada função mostra apenas quem a exerce.')
                ->icon('heroicon-o-user-group')
                ->schema([
                    CheckboxList::make('funcoes')
                        ->label('Funções que exerce')
                        ->relationship('funcoes', 'nome')
                        ->options(Funcao::where('ativo', true)->orderBy('ordem')->pluck('nome', 'id'))
                        ->columns(3)
                        ->gridDirection('row'),
                ]),

            Section::make('Perfil de Cantor')
                ->description('Preenchido automaticamente no portal. Complete as informações abaixo.')
                ->icon('heroicon-o-microphone')
                ->columns(2)
                ->visible(function (Get $get) {
                    return in_array('Cantor', (array) ($get('roles') ?? []));
                })
                ->schema([
                    Select::make('voz')
                        ->label('Tipo de voz')
                        ->options([
                            'soprano' => 'Soprano',
                            'contralto' => 'Contralto',
                            'tenor' => 'Tenor',
                            'baritono' => 'Barítono',
                            'baixo' => 'Baixo',
                            'outro' => 'Outro',
                        ])
                        ->searchable()
                        ->nullable(),

                    Textarea::make('observacoes')
                        ->label('Observações')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Section::make('Endereço')
                ->columns(3)
                ->collapsed()
                ->schema([
                    TextInput::make('cep')
                        ->label('CEP')
                        ->mask('99999-999')
                        ->maxLength(9),

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
        ]);
    }
}
