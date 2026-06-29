<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoCantor extends Model
{
    protected $table = 'historico_cantores';

    protected $fillable = [
        'cantor_id', 'culto_id', 'musica_id',
        'nome_musica', 'artista', 'tom', 'data_culto', 'tipo_culto', 'observacao',
    ];

    protected function casts(): array
    {
        return ['data_culto' => 'date'];
    }

    public function cantor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Cantor::class);
    }

    public function culto(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Culto::class);
    }

    public function musica(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Musica::class);
    }
}
