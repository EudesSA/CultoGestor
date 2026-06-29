<?php

namespace App\Filament\Resources\Cultos;

use App\Filament\Resources\Cultos\Pages\CreateCulto;
use App\Filament\Resources\Cultos\Pages\EditCulto;
use App\Filament\Resources\Cultos\Pages\ListCultos;
use App\Filament\Resources\Cultos\RelationManagers\EscalasRelationManager;
use App\Filament\Resources\Cultos\RelationManagers\LiturgiaRelationManager;
use App\Filament\Resources\Cultos\Schemas\CultoForm;
use App\Filament\Resources\Cultos\Tables\CultosTable;
use App\Models\Culto;
use BackedEnum;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class CultoResource extends Resource
{
    protected static ?string $model = Culto::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static UnitEnum|string|null $navigationGroup = 'Programação';
    protected static ?string $navigationLabel = 'Cultos';
    protected static ?string $modelLabel = 'Culto';
    protected static ?string $pluralModelLabel = 'Cultos';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return CultoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Programação')
                ->columns(2)
                ->schema([
                    TextEntry::make('tipo.nome')
                        ->label('Tipo')
                        ->badge()
                        ->color(fn ($record) => 'primary'),

                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'realizado' => 'success',
                            'cancelado' => 'danger',
                            default     => 'warning',
                        })
                        ->formatStateUsing(fn (string $state) => match ($state) {
                            'agendado'  => 'Agendado',
                            'realizado' => 'Realizado',
                            'cancelado' => 'Cancelado',
                            default     => $state,
                        }),

                    TextEntry::make('data')
                        ->label('Data')
                        ->date('l, d/m/Y'),

                    Grid::make(2)->schema([
                        TextEntry::make('hora_inicio')
                            ->label('Início')
                            ->formatStateUsing(fn ($state) => $state ? substr($state, 0, 5) : '—'),

                        TextEntry::make('hora_fim')
                            ->label('Fim')
                            ->formatStateUsing(fn ($state) => $state ? substr($state, 0, 5) : '—'),
                    ]),

                    TextEntry::make('tema')
                        ->label('Tema / Pregação')
                        ->placeholder('Não informado')
                        ->columnSpanFull(),

                    TextEntry::make('local')
                        ->label('Local')
                        ->placeholder('Não informado'),

                    TextEntry::make('google_meet_link')
                        ->label('Link de Transmissão')
                        ->placeholder('Não informado')
                        ->url(fn ($state) => $state)
                        ->openUrlInNewTab(),

                    TextEntry::make('observacoes')
                        ->label('Observações')
                        ->placeholder('Nenhuma')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return CultosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            LiturgiaRelationManager::class,
            EscalasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCultos::route('/'),
            'create' => CreateCulto::route('/create'),
            'edit'   => EditCulto::route('/{record}/edit'),
        ];
    }
}

