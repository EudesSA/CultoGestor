<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnuncioMidia extends Model
{
    protected $fillable = [
        'anuncio_id', 'tipo', 'nome_original', 'path_local',
        'path_drive', 'drive_file_id', 'tamanho_bytes', 'mime_type', 'ordem',
    ];

    public function anuncio(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Anuncio::class);
    }

    public function getTamanhoFormatadoAttribute(): string
    {
        if (! $this->tamanho_bytes) {
            return '—';
        }
        $mb = $this->tamanho_bytes / 1024 / 1024;
        return number_format($mb, 1) . ' MB';
    }
}
