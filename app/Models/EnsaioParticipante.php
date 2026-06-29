<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EnsaioParticipante extends Model
{
    protected $table = 'ensaio_participantes';

    protected $fillable = [
        'ensaio_id', 'user_id', 'status', 'confirmado_em', 'token_confirmacao', 'observacao',
    ];

    protected function casts(): array
    {
        return ['confirmado_em' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (EnsaioParticipante $p) {
            if (! $p->token_confirmacao) {
                $p->token_confirmacao = Str::uuid()->toString();
            }
        });
    }

    public function ensaio(): BelongsTo
    {
        return $this->belongsTo(Ensaio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
