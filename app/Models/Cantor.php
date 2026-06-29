<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cantor extends Model
{
    protected $table = 'cantores';

    protected $fillable = [
        'user_id', 'foto', 'nome', 'email', 'telefone',
        'voz', 'observacoes', 'ativo', 'token_portal',
    ];

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (Cantor $cantor) {
            if (! $cantor->token_portal) {
                $cantor->token_portal = Str::uuid()->toString();
            }
        });
    }

    public function portalUrl(): string
    {
        return route('cantor.portal', $this->token_portal);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function musicas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Musica::class);
    }

    public function escalas(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Escala::class,
            User::class,
            'id',         // FK em users
            'user_id',    // FK em escalas
            'user_id',    // local key em cantores
            'id'          // local key em users
        );
    }

    public function historico(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HistoricoCantor::class)->latest('data_culto');
    }
}
