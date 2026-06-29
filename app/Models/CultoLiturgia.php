<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CultoLiturgia extends Model
{
    protected $fillable = [
        'culto_id', 'ordem', 'tipo', 'titulo', 'horario_previsto',
        'duracao_minutos', 'observacao', 'referencia_type', 'referencia_id',
        'concluido', 'iniciado_em', 'concluido_em',
    ];

    protected function casts(): array
    {
        return [
            'concluido'    => 'boolean',
            'iniciado_em'  => 'datetime',
            'concluido_em' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Define a próxima ordem automaticamente se não vier preenchida.
        static::creating(function (CultoLiturgia $liturgia) {
            if (empty($liturgia->ordem)) {
                $liturgia->ordem = (int) static::where('culto_id', $liturgia->culto_id)->max('ordem') + 1;
            }
        });
    }

    public function culto(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Culto::class);
    }

    public function referencia(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
