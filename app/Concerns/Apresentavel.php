<?php

namespace App\Concerns;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Dá a um model a capacidade de ser "apresentado" em tela cheia
 * (imagem, vídeo, PDF ou YouTube) — usado pelos botões "Abrir" e
 * "Apresentar em monitor". Resolve a fonte a partir do link do YouTube
 * ou do primeiro arquivo enviado (Media Library).
 */
trait Apresentavel
{
    /** Nome da coleção Media Library que guarda o arquivo apresentável. */
    protected function colecaoApresentacao(): string
    {
        return 'arquivo';
    }

    /** Título amigável (models usam `titulo` ou `nome`). */
    protected function tituloApresentacao(): string
    {
        return (string) ($this->titulo ?? $this->nome ?? 'Apresentação');
    }

    public function midiaApresentacao(): ?Media
    {
        if (! $this instanceof HasMedia) {
            return null;
        }

        return $this->getFirstMedia($this->colecaoApresentacao());
    }

    public static function tipoPorMime(?string $mime): string
    {
        return match (true) {
            str_starts_with((string) $mime, 'image/') => 'imagem',
            str_starts_with((string) $mime, 'video/') => 'video',
            str_starts_with((string) $mime, 'audio/') => 'audio',
            $mime === 'application/pdf'                => 'pdf',
            default                                    => 'arquivo',
        };
    }

    /** youtube | imagem | video | audio | pdf | null */
    public function tipoApresentacao(): ?string
    {
        if (! empty($this->link_youtube)) {
            return 'youtube';
        }

        $midia = $this->midiaApresentacao();

        return $midia ? static::tipoPorMime($midia->mime_type) : null;
    }

    public function temApresentacao(): bool
    {
        return $this->tipoApresentacao() !== null;
    }

    /** URL da página de apresentação em tela cheia, ou null. */
    public function urlApresentacao(): ?string
    {
        $tipo = $this->tipoApresentacao();
        if (! $tipo) {
            return null;
        }

        if ($tipo === 'youtube') {
            return route('apresentar', [
                'tipo'   => 'youtube',
                'src'    => $this->link_youtube,
                'titulo' => $this->tituloApresentacao(),
            ]);
        }

        $midia = $this->midiaApresentacao();

        return route('apresentar', [
            'tipo'   => $tipo,
            'src'    => route('apresentar.midia', $midia),
            'titulo' => $this->tituloApresentacao(),
        ]);
    }

    /** Link bruto para abrir em nova aba (YouTube ou stream do arquivo). */
    public function urlAbrir(): ?string
    {
        if (! empty($this->link_youtube)) {
            return $this->link_youtube;
        }

        $midia = $this->midiaApresentacao();

        return $midia ? route('apresentar.midia', $midia) : null;
    }
}
