<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HinoExecucao extends Model
{
    protected $table = 'hino_execucoes';

    protected $fillable = ['hino_id', 'culto_id', 'tom', 'executado_em'];

    protected function casts(): array
    {
        return ['executado_em' => 'date'];
    }

    public function hino(): BelongsTo
    {
        return $this->belongsTo(Hino::class);
    }

    public function culto(): BelongsTo
    {
        return $this->belongsTo(Culto::class);
    }
}
