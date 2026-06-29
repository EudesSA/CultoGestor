<?php

namespace App\Models;

use App\Concerns\Apresentavel;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Anuncio extends Model implements HasMedia
{
    use InteractsWithMedia;
    use Apresentavel;

    protected $fillable = [
        'igreja_id',
        'culto_id', 'titulo', 'tipo', 'categoria', 'descricao',
        'link_youtube', 'youtube_canal', 'youtube_thumbnail', 'duracao_segundos',
        'data_inicio', 'data_fim', 'ativo', 'sempre_disponivel', 'ordem',
    ];

    public function igreja(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Igreja::class);
    }

    /** A apresentação usa o primeiro arquivo da coleção de mídias. */
    protected function colecaoApresentacao(): string
    {
        return 'midias';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile()
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

        $this->addMediaCollection('midias')
            ->useDisk('cultos')
            ->acceptsMimeTypes([
                'image/jpeg', 'image/png', 'image/webp', 'image/gif',
                'video/mp4', 'video/quicktime', 'video/avi',
                'application/pdf',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            ]);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(150)
            ->nonQueued();
    }

    protected function casts(): array
    {
        return [
            'data_inicio'       => 'date',
            'data_fim'          => 'date',
            'ativo'             => 'boolean',
            'sempre_disponivel' => 'boolean',
        ];
    }

    public function culto(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Culto::class);
    }

    public function midias(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AnuncioMidia::class)->orderBy('ordem');
    }

    public function scopeAtivos($query): void
    {
        $today = now()->toDateString();
        $query->where('ativo', true)->where(function ($q) use ($today) {
            $q->where('sempre_disponivel', true)
              ->orWhere(function ($q2) use ($today) {
                  $q2->where('data_inicio', '<=', $today)
                     ->where('data_fim', '>=', $today);
              });
        });
    }
}
