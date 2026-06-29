<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ensaio extends Model
{
    protected $fillable = [
        'culto_id', 'data_hora', 'local', 'observacoes', 'status',
    ];

    protected function casts(): array
    {
        return ['data_hora' => 'datetime'];
    }

    public function culto(): BelongsTo
    {
        return $this->belongsTo(Culto::class);
    }

    public function participantes(): HasMany
    {
        return $this->hasMany(EnsaioParticipante::class);
    }

    public function musicas(): HasMany
    {
        return $this->hasMany(EnsaioMusica::class)->orderBy('ordem');
    }

    public function getRotuloAttribute(): string
    {
        $quando = $this->data_hora?->format('d/m/Y H:i') ?? '—';
        $culto  = $this->culto?->tipo?->nome;

        return 'Ensaio ' . $quando . ($culto ? " · {$culto}" : '');
    }
}
