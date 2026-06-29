<?php

namespace App\Models;

use App\Concerns\Apresentavel;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Musica extends Model implements HasMedia
{
    use Apresentavel;
    use InteractsWithMedia;

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
        'cantor_id', 'culto_id', 'nome', 'artista', 'tom',
        'duracao_segundos', 'link_youtube', 'status',
        'observacoes_diretor', 'token_acesso', 'enviado_em', 'aprovado_em',
    ];

    public function igreja(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Igreja::class);
    }

    protected function casts(): array
    {
        return [
            'enviado_em'  => 'datetime',
            'aprovado_em' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Musica $musica) {
            if (! $musica->token_acesso) {
                $musica->token_acesso = \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function cantor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Cantor::class);
    }

    public function culto(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Culto::class);
    }

    public function arquivos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MusicaArquivo::class);
    }

    public function getDuracaoFormatadaAttribute(): ?string
    {
        if (! $this->duracao_segundos) {
            return null;
        }
        return gmdate('i:s', $this->duracao_segundos);
    }

    /**
     * Categoria (pasta) de um arquivo conforme a Especificação Mestra (Módulo 3).
     * Playback tem pasta própria; os demais arquivos da música especial
     * (mp3, letra, cifra, partitura, outro) ficam em "Musica Especial".
     */
    public static function categoriaArquivo(string $tipo): string
    {
        return match ($tipo) {
            'playback' => 'Playback',
            default    => 'Musica Especial',
        };
    }

    /**
     * Diretório do arquivo no disk `cultos`, organizado pela DATA DO CULTO:
     *   Cultos/{YYYY}/{MM}/{DD-MM-YYYY}/{categoria}/
     * Fonte única usada tanto pelo Portal do Cantor quanto pelo painel do Diretor.
     */
    public function diretorioArquivo(string $tipo): string
    {
        $data = $this->culto?->data ?? now();

        return sprintf(
            'Cultos/%s/%s/%s/%s',
            $data->format('Y'),
            $data->format('m'),
            $data->format('d-m-Y'),
            self::categoriaArquivo($tipo),
        );
    }
}
