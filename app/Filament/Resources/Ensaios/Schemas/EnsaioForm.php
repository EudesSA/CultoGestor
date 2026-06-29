<?php

namespace App\Filament\Resources\Ensaios\Schemas;

use App\Models\Culto;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EnsaioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados do Ensaio')
                ->columns(2)
                ->schema([
                    Select::make('culto_id')
                        ->label('Culto que será preparado')
                        ->placeholder('Próximo culto…')
                        ->options(
                            fn () => Culto::with('tipo')
                                ->where('data', '>=', now()->toDateString())
                                ->orderBy('data')
                                ->get()
                                ->mapWithKeys(fn ($c) => [
                                    $c->id => ($c->tipo?->nome ?? 'Culto') . ' — ' . $c->data?->format('d/m/Y'),
                                ])
                        )
                        ->searchable()
                        ->nullable()
                        ->columnSpanFull(),

                    DateTimePicker::make('data_hora')
                        ->label('Data e hora do ensaio')
                        ->required()
                        ->seconds(false)
                        ->displayFormat('d/m/Y H:i'),

                    TextInput::make('local')
                        ->label('Local')
                        ->maxLength(255),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'agendado'  => 'Agendado',
                            'realizado' => 'Realizado',
                            'cancelado' => 'Cancelado',
                        ])
                        ->default('agendado')
                        ->required(),

                    Textarea::make('observacoes')
                        ->label('Observações')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
