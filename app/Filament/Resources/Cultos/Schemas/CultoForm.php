<?php

namespace App\Filament\Resources\Cultos\Schemas;

use App\Models\CultoTipo;
use App\Models\Igreja;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CultoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Culto')
                    ->columns(2)
                    ->schema([
                        Select::make('culto_tipo_id')
                            ->label('Tipo de Culto')
                            ->relationship('tipo', 'nome')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'agendado' => 'Agendado',
                                'realizado' => 'Realizado',
                                'cancelado' => 'Cancelado',
                            ])
                            ->default('agendado')
                            ->required()
                            ->columnSpan(1),

                        DatePicker::make('data')
                            ->label('Data')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->columnSpan(1),

                        Grid::make(2)
                            ->schema([
                                TimePicker::make('hora_inicio')
                                    ->label('Início')
                                    ->required()
                                    ->seconds(false),

                                TimePicker::make('hora_fim')
                                    ->label('Fim')
                                    ->seconds(false),
                            ])
                            ->columnSpan(1),

                        TextInput::make('tema')
                            ->label('Tema / Pregação')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('local')
                            ->label('Local')
                            ->maxLength(255)
                            ->placeholder('Ex: Templo Sede, Salão Anexo')
                            ->datalist(fn () => Igreja::where('ativa', true)->pluck('nome')->all())
                            ->columnSpan(1),

                        TextInput::make('google_meet_link')
                            ->label('Link de Transmissão')
                            ->url()
                            ->columnSpan(1),

                        Textarea::make('observacoes')
                            ->label('Observações')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
