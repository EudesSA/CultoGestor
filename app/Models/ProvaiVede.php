<?php

namespace App\Models;

use App\Concerns\Apresentavel;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProvaiVede extends Model implements HasMedia
{
    use InteractsWithMedia;
    use Apresentavel;

    protected $table = 'provai_vede';

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
        'trimestre',
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

    /** Usuários que marcaram este vídeo como favorito. */
    public function favoritadoPor(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'provai_vede_favoritos',
            'provai_vede_id',
            'user_id'
        )->withTimestamps();
    }

    /** Indica se o usuário (padrão: autenticado) favoritou este vídeo. */
    public function isFavorito(?int $userId = null): bool
    {
        $userId ??= auth()->id();

        if (! $userId) {
            return false;
        }

        return $this->relationLoaded('favoritadoPor')
            ? $this->favoritadoPor->contains('id', $userId)
            : $this->favoritadoPor()->where('user_id', $userId)->exists();
    }

    /** Alterna o favorito do usuário autenticado; retorna o novo estado. */
    public function alternarFavorito(?int $userId = null): bool
    {
        $userId ??= auth()->id();
        if (! $userId) {
            return false;
        }

        $this->favoritadoPor()->toggle($userId);
        $this->unsetRelation('favoritadoPor');

        return $this->isFavorito($userId);
    }

    protected function casts(): array
    {
        return [
            'data_exibicao' => 'date',
            'ativo' => 'boolean',
        ];
    }

    public function getDuracaoFormatadaAttribute(): ?string
    {
        if (!$this->duracao_segundos) {
            return null;
        }
        return gmdate('i:s', $this->duracao_segundos);
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
