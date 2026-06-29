<?php

namespace App\Filament\Resources\Escalas\Schemas;

use App\Models\Culto;
use App\Models\Funcao;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EscalaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Culto')
                ->schema([
                    Select::make('culto_id')
                        ->label('Culto')
                        ->options(fn () => Culto::with('tipo')
                            ->where('data', '>=', now()->subMonths(1))
                            ->orderBy('data', 'desc')
                            ->get()
                            ->mapWithKeys(fn ($c) => [
                                $c->id => ($c->tipo?->nome ?? 'Culto') . ' — ' . $c->data?->format('d/m/Y'),
                            ])
                        )
                        ->searchable()
                        ->required()
                        ->columnSpanFull(),
                ]),

            Section::make('Equipe')
                ->columns(2)
                ->schema(self::camposFuncoes()),
        ]);
    }

    public static function camposFuncoes(): array
    {
        $funcoes = Funcao::where('ativo', true)->orderBy('ordem')->get();
        $fields  = [];

        foreach ($funcoes as $funcao) {
            $fields[] = Select::make("funcao_{$funcao->id}")
                ->label($funcao->nome)
                ->options($funcao->opcoesUsuarios())
                ->helperText($funcao->helperUsuarios())
                ->searchable()
                ->nullable()
                ->placeholder('Não atribuído');
        }

        return $fields;
    }
}
