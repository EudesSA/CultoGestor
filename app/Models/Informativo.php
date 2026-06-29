<?php

namespace App\Models;

use App\Concerns\Apresentavel;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Informativo extends Model implements HasMedia
{
    use InteractsWithMedia;
    use Apresentavel;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('arquivo')
            ->singleFile()
            ->useDisk('cultos')
            ->acceptsMimeTypes([
                'video/mp4', 'video/quicktime', 'video/webm',
                'image/jpeg', 'image/png', 'image/webp', 'image/gif',
                'application/pdf',
                'audio/mpeg', 'audio/wav', 'audio/ogg',
            ]);
    }

    protected $fillable = [
        'igreja_id',
        'culto_id',
        'titulo',
        'tema',
        'categoria',
        'data_exibicao',
        'duracao_segundos',
        'link_youtube',
        'thumbnail_path',
        'youtube_canal',
        'youtube_thumbnail',
        'status_importacao',
        'ativo',
    ];

    public function culto(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Culto::class);
    }

    public function igreja(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Igreja::class);
    }

    protected function casts(): array
    {
        return [
            'data_exibicao' => 'date',
            'ativo' => 'boolean',
        ];
    }

    public function getYoutubeIdAttribute(): ?string
    {
        if (!$this->link_youtube) {
            return null;
        }
        preg_match('/(?:v=|youtu\.be\/)([A-Za-z0-9_-]{11})/', $this->link_youtube, $m);
        return $m[1] ?? null;
    }
}
