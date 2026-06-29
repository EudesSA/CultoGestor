<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProvaiVedePlaylist extends Model
{
    protected $table = 'provai_vede_playlists';

    protected $fillable = ['nome', 'playlist_id', 'ativo'];

    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }

    /**
     * Aceita colar a URL inteira da playlist (ou do vídeo com &list=...) e
     * guarda apenas o ID (PL...). Se já for o ID puro, mantém.
     */
    public function setPlaylistIdAttribute($value): void
    {
        $v = trim((string) $value);

        if (preg_match('/[?&]list=([A-Za-z0-9_-]+)/', $v, $m)) {
            $v = $m[1];
        }

        $this->attributes['playlist_id'] = $v;
    }

    /** URL do feed RSS (Atom) da playlist no YouTube. */
    public function feedUrl(): string
    {
        return 'https://www.youtube.com/feeds/videos.xml?playlist_id=' . $this->playlist_id;
    }

    /** Feeds ativos cadastrados (vazio = usar fallback do .env). */
    public static function feedsAtivos(): array
    {
        return static::where('ativo', true)
            ->get()
            ->map(fn (self $p) => $p->feedUrl())
            ->all();
    }
}
