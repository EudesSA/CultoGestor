<?php

namespace App\Filament\Resources\Musicas;

use App\Filament\Resources\Musicas\Pages\CreateMusica;
use App\Filament\Resources\Musicas\Pages\EditMusica;
use App\Filament\Resources\Musicas\Pages\ListMusicas;
use App\Filament\Resources\Musicas\Schemas\MusicaForm;
use App\Filament\Resources\Musicas\RelationManagers\ArquivosRelationManager;
use App\Filament\Resources\Musicas\Tables\MusicasTable;
use App\Models\Musica;
use App\Models\User;
use Filament\Resources\Resource;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MusicaResource extends Resource
{
    protected static ?string $model = Musica::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-musical-note';
    protected static UnitEnum|string|null $navigationGroup = 'Músicas & Cantores';
    protected static ?string $navigationLabel = 'Músicas';
    protected static ?string $modelLabel = 'Música';
    protected static ?string $pluralModelLabel = 'Músicas';
    protected static ?int $navigationSort = 1;

    /**
     * Recorte de visibilidade: um Cantor enxerga apenas as próprias músicas.
     * Administrador e demais roles operacionais veem todas. (Spec, seção 7)
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();

        if ($user && $user->hasRole('Cantor') && ! $user->hasRole('Administrador')) {
            $query->whereHas('cantor', fn (Builder $q) => $q->where('user_id', $user->id));
        }

        return $query;
    }

    /**
     * Papéis que podem gerenciar músicas de qualquer cantor e alterar o
     * status (aprovar / reprovar / revisar). (Spec, seção 10 — Portal do Cantor)
     */
    public static function podeGerenciarMusicas(?User $user = null): bool
    {
        $user ??= auth()->user();

        return (bool) $user?->hasAnyRole(['Administrador', 'Diretor de Música', 'Sonoplasta']);
    }

    /**
     * Cantor "restrito": logado como Cantor e sem papel de gestão.
     * Só pode mexer nas próprias músicas e não pode escolher outro cantor
     * nem alterar o status.
     */
    public static function cantorRestrito(?User $user = null): bool
    {
        $user ??= auth()->user();

        return $user && $user->hasRole('Cantor') && ! static::podeGerenciarMusicas($user);
    }

    public static function form(Schema $schema): Schema
    {
        return MusicaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MusicasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ArquivosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMusicas::route('/'),
            'create' => CreateMusica::route('/create'),
            'edit' => EditMusica::route('/{record}/edit'),
        ];
    }
}

