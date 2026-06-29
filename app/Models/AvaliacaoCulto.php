<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvaliacaoCulto extends Model
{
    protected $table = 'avaliacoes_culto';

    protected $fillable = [
        'culto_id', 'user_id', 'nota_geral',
        'nota_som', 'nota_projecao', 'nota_transmissao', 'observacoes',
    ];

    public function culto(): BelongsTo
    {
        return $this->belongsTo(Culto::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Média das notas preenchidas (geral + técnicas). */
    public function getMediaAttribute(): float
    {
        $notas = array_filter([
            $this->nota_geral,
            $this->nota_som,
            $this->nota_projecao,
            $this->nota_transmissao,
        ], fn ($n) => $n !== null);

        return $notas ? round(array_sum($notas) / count($notas), 1) : 0.0;
    }
}
