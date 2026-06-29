<?php

namespace App\Models;

use App\Concerns\Apresentavel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hino extends Model
{
    use Apresentavel;

    protected $fillable = [
        'numero', 'titulo', 'hinario', 'tom',
        'tons_alternativos', 'link_youtube', 'observacoes',
    ];

    public function execucoes(): HasMany
    {
        return $this->hasMany(HinoExecucao::class);
    }

    /** Título amigável p/ apresentação (trait Apresentavel usa `titulo`). */

    /**
     * Tom mais tocado historicamente (moda das execuções).
     * Cai no tom padrão do hino quando não há histórico.
     */
    public function tomMaisTocado(): ?string
    {
        $moda = $this->execucoes()
            ->whereNotNull('tom')
            ->selectRaw('tom, COUNT(*) as total')
            ->groupBy('tom')
            ->orderByDesc('total')
            ->value('tom');

        return $moda ?: $this->tom;
    }

    /** Registra uma execução (append-only) para alimentar a sugestão de tom. */
    public function registrarExecucao(?string $tom = null, ?int $cultoId = null): HinoExecucao
    {
        return $this->execucoes()->create([
            'tom'          => $tom ?: $this->tom,
            'culto_id'     => $cultoId,
            'executado_em' => now()->toDateString(),
        ]);
    }

    public function getRotuloAttribute(): string
    {
        return "#{$this->numero} — {$this->titulo}";
    }
}
