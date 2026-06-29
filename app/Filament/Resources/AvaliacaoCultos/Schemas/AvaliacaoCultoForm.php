<?php

namespace App\Filament\Resources\AvaliacaoCultos\Schemas;

use App\Models\Culto;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class AvaliacaoCultoForm
{
    private const NOTAS = [
        5 => '★★★★★ (5)',
        4 => '★★★★ (4)',
        3 => '★★★ (3)',
        2 => '★★ (2)',
        1 => '★ (1)',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Hidden::make('user_id')->default(fn () => Auth::id()),

            Section::make('Culto avaliado')
                ->schema([
                    Select::make('culto_id')
                        ->label('Culto')
                        ->options(
                            fn () => Culto::with('tipo')
                                ->where('data', '<=', now()->addDay())
                                ->orderByDesc('data')
                                ->limit(60)
                                ->get()
                                ->mapWithKeys(fn ($c) => [
                                    $c->id => ($c->tipo?->nome ?? 'Culto') . ' — ' . $c->data?->format('d/m/Y'),
                                ])
                        )
                        ->searchable()
                        ->required()
                        ->columnSpanFull(),
                ]),

            Section::make('Notas (1 a 5 estrelas)')
                ->columns(2)
                ->schema([
                    Select::make('nota_geral')
                        ->label('Avaliação geral')
                        ->options(self::NOTAS)
                        ->required(),

                    Select::make('nota_som')
                        ->label('Sonoplastia')
                        ->options(self::NOTAS),

                    Select::make('nota_projecao')
                        ->label('Projeção')
                        ->options(self::NOTAS),

                    Select::make('nota_transmissao')
                        ->label('Transmissão')
                        ->options(self::NOTAS),

                    Textarea::make('observacoes')
                        ->label('Observações')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
