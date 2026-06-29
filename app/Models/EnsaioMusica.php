<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnsaioMusica extends Model
{
    protected $table = 'ensaio_musicas';

    protected $fillable = [
        'ensaio_id', 'musica_id', 'nome', 'tom', 'ordem', 'observacao',
    ];

    public function ensaio(): BelongsTo
    {
        return $this->belongsTo(Ensaio::class);
    }

    public function musica(): BelongsTo
    {
        return $this->belongsTo(Musica::class);
    }
}
