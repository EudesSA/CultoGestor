<?php

namespace App\Filament\Resources\Cantors\RelationManagers;

use App\Models\Culto;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MusicasRelationManager extends RelationManager
{
    protected static string $relationship = 'musicas';

    protected static ?string $title = 'Playlist do Cantor';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('culto_id')
                    ->label('Culto')
                    ->options(fn () => Culto::with('tipo')
                        ->where('data', '>=', now()->subMonths(2))
                        ->orderBy('data', 'desc')
                        ->get()
                        ->mapWithKeys(fn ($c) => [$c->id => $c->tipo?->nome . ' — ' . $c->data?->format('d/m/Y')])
                    )
                    ->searchable()
                    ->placeholder('Selecione o culto')
                    ->columnSpanFull(),

                TextInput::make('nome')
                    ->label('Nome da música')
                    ->required()
                    ->maxLength(255),

                TextInput::make('artista')
                    ->label('Artista / Intérprete original')
                    ->maxLength(255),

                TextInput::make('tom')
                    ->label('Tom')
                    ->maxLength(10)
                    ->placeholder('Ex: Dó, Sol, Lá♭'),

                TextInput::make('duracao_segundos')
                    ->label('Duração (segundos)')
                    ->numeric()
                    ->minValue(0),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pendente' => 'Pendente',
                        'enviado'  => 'Enviado',
                        'revisado' => 'Revisado',
                        'aprovado' => 'Aprovado',
                    ])
                    ->default('pendente')
                    ->required(),

                TextInput::make('link_youtube')
                    ->label('Link YouTube')
                    ->url()
                    ->columnSpanFull(),

                Textarea::make('observacoes_diretor')
                    ->label('Observações do Diretor')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->label('Música')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('artista')
                    ->label('Artista')
                    ->searchable()
                    ->limit(25),

                TextColumn::make('tom')
                    ->label('Tom')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('culto.data')
                    ->label('Culto')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'aprovado' => 'success',
                        'revisado' => 'info',
                        'enviado'  => 'warning',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'aprovado' => 'Aprovado',
                        'revisado' => 'Em revisão',
                        'enviado'  => 'Enviado',
                        default    => 'Pendente',
                    }),

                TextColumn::make('duracao_formatada')
                    ->label('Duração'),

                TextColumn::make('created_at')
                    ->label('Adicionada')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pendente' => 'Pendente',
                        'enviado'  => 'Enviado',
                        'revisado' => 'Revisado',
                        'aprovado' => 'Aprovado',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()->label('Adicionar música'),
            ])
            ->actions([
                Action::make('aprovar')
                    ->label('Aprovar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status !== 'aprovado')
                    ->action(fn ($record) => $record->update([
                        'status'      => 'aprovado',
                        'aprovado_em' => now(),
                    ])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
