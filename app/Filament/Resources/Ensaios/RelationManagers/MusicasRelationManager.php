<?php

namespace App\Filament\Resources\Ensaios\RelationManagers;

use App\Models\Musica;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MusicasRelationManager extends RelationManager
{
    protected static string $relationship = 'musicas';

    protected static ?string $title = 'Músicas do ensaio';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('musica_id')
                ->label('Vincular música cadastrada (opcional)')
                ->options(fn () => Musica::query()
                    ->with('cantor')
                    ->latest('id')
                    ->limit(100)
                    ->get()
                    ->mapWithKeys(fn ($m) => [$m->id => $m->nome . ($m->cantor ? " — {$m->cantor->nome}" : '')]))
                ->searchable()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state && $musica = Musica::find($state)) {
                        $set('nome', $musica->nome);
                        $set('tom', $musica->tom);
                    }
                })
                ->columnSpanFull(),

            TextInput::make('nome')
                ->label('Nome da música')
                ->required()
                ->maxLength(255),

            TextInput::make('tom')
                ->label('Tom')
                ->maxLength(10),

            TextInput::make('ordem')
                ->label('Ordem')
                ->numeric()
                ->default(0),

            TextInput::make('observacao')
                ->label('Observação')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nome')
            ->defaultSort('ordem')
            ->reorderable('ordem')
            ->columns([
                TextColumn::make('ordem')->label('#')->sortable(),
                TextColumn::make('nome')->label('Música')->searchable(),
                TextColumn::make('tom')->label('Tom')->badge()->placeholder('—'),
                TextColumn::make('observacao')->label('Observação')->placeholder('—')->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Adicionar música'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
