<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LouvorjaExportacao extends Model
{
    protected $table = 'louvorja_exportacoes';

    protected $fillable = [
        'culto_id', 'gerado_por', 'path_arquivo',
        'path_drive', 'total_itens', 'gerado_em',
    ];

    protected function casts(): array
    {
        return ['gerado_em' => 'datetime'];
    }

    public function culto(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Culto::class);
    }

    public function geradoPor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'gerado_por');
    }
}
