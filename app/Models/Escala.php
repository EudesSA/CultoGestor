<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Escala extends Model
{
    protected $fillable = [
        'igreja_id',
        'culto_id', 'funcao_id', 'user_id',
        'token_confirmacao', 'status', 'confirmado_em', 'observacao',
    ];

    public function igreja(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Igreja::class);
    }

    protected function casts(): array
    {
        return [
            'confirmado_em' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Escala $escala) {
            if (! $escala->token_confirmacao) {
                $escala->token_confirmacao = \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function culto(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Culto::class);
    }

    public function funcao(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Funcao::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
