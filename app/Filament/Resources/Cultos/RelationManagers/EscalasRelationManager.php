<?php

namespace App\Filament\Resources\Cultos\RelationManagers;

use App\Models\Escala;
use App\Models\Funcao;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EscalasRelationManager extends RelationManager
{
    protected static string $relationship = 'escalas';

    protected static ?string $title = 'Equipe / Escala';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('funcao_id')
                ->label('Função')
                ->options(
                    fn () => Funcao::where('ativo', true)
                        ->orderBy('ordem')
                        ->pluck('nome', 'id')
                )
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('user_id', null))
                ->required(),

            Select::make('user_id')
                ->label('Membro')
                ->options(function (Get $get) {
                    $funcaoId = $get('funcao_id');
                    return self::opcoesUsuario($funcaoId ? Funcao::find($funcaoId) : null);
                })
                ->searchable()
                ->required()
                ->helperText(function (Get $get) {
                    $funcaoId = $get('funcao_id');
                    if (! $funcaoId) return null;
                    $funcao = Funcao::find($funcaoId);
                    return self::helperUsuario($funcao);
                }),

            Select::make('status')
                ->label('Status')
                ->options([
                    'pendente'   => 'Pendente',
                    'confirmado' => 'Confirmado',
                    'recusado'   => 'Recusado',
                ])
                ->default('pendente')
                ->required(),

            Textarea::make('observacao')
                ->label('Observação')
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('funcao.nome')
                    ->label('Função')
                    ->badge()
                    ->color(fn ($record) => $this->corFuncao($record->funcao?->cor))
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Membro')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmado' => 'success',
                        'recusado'   => 'danger',
                        default      => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'confirmado' => 'Confirmado',
                        'recusado'   => 'Recusado',
                        default      => 'Pendente',
                    }),

                TextColumn::make('confirmado_em')
                    ->label('Confirmado em')
                    ->dateTime('d/m H:i')
                    ->placeholder('—'),
            ])
            ->defaultSort(
                fn ($query) => $query->orderBy(
                    \App\Models\Funcao::select('ordem')
                        ->whereColumn('funcoes.id', 'escalas.funcao_id')
                        ->limit(1)
                )
            )
            ->headerActions([
                Action::make('montar_equipe')
                    ->label('Montar Equipe')
                    ->icon('heroicon-o-user-group')
                    ->color('primary')
                    ->form(fn () => $this->buildEquipeForm())
                    ->fillForm(fn () => $this->fillEquipeForm())
                    ->action(fn (array $data) => $this->salvarEquipe($data))
                    ->modalHeading('Montar Equipe do Culto')
                    ->modalSubmitActionLabel('Salvar Equipe')
                    ->modalWidth('lg'),

                CreateAction::make()
                    ->label('Adicionar membro')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    // --- Montar Equipe helpers ---

    private function buildEquipeForm(): array
    {
        $funcoes = Funcao::where('ativo', true)->orderBy('ordem')->get();
        $fields  = [];

        foreach ($funcoes as $funcao) {
            $fields[] = Select::make("funcao_{$funcao->id}")
                ->label($funcao->nome)
                ->options(self::opcoesUsuario($funcao))
                ->helperText($funcao->helperUsuarios())
                ->multiple()           // permite 2+ pessoas na mesma função
                ->searchable()
                ->nullable()
                ->placeholder('Não atribuído');
        }

        return $fields;
    }

    private function fillEquipeForm(): array
    {
        $cultoId = $this->getOwnerRecord()->id;
        $escalas = Escala::where('culto_id', $cultoId)->get()->groupBy('funcao_id');
        $data    = [];

        foreach ($escalas as $funcaoId => $grupo) {
            $data["funcao_{$funcaoId}"] = $grupo->pluck('user_id')->all();
        }

        return $data;
    }

    private function salvarEquipe(array $data): void
    {
        $cultoId = $this->getOwnerRecord()->id;
        $funcoes = Funcao::where('ativo', true)->get();

        foreach ($funcoes as $funcao) {
            $userIds = array_values(array_filter((array) ($data["funcao_{$funcao->id}"] ?? [])));

            // Remove escalas de quem saiu desta função (preserva os mantidos).
            Escala::where('culto_id', $cultoId)
                ->where('funcao_id', $funcao->id)
                ->whereNotIn('user_id', $userIds ?: [0])
                ->delete();

            // Adiciona os novos sem duplicar (mantém status/token de quem já estava).
            foreach ($userIds as $userId) {
                Escala::firstOrCreate(
                    ['culto_id' => $cultoId, 'funcao_id' => $funcao->id, 'user_id' => $userId],
                    ['status' => 'pendente']
                );
            }
        }
    }

    private function corFuncao(?string $hex): string
    {
        return 'primary';
    }

    /**
     * Opções de membro filtradas pelas habilidades da função (Funcao::opcoesUsuarios).
     */
    private static function opcoesUsuario(?Funcao $funcao): array
    {
        return $funcao
            ? $funcao->opcoesUsuarios()
            : User::orderBy('name')->pluck('name', 'id')->toArray();
    }

    private static function helperUsuario(?Funcao $funcao): ?string
    {
        return $funcao?->helperUsuarios();
    }
}
