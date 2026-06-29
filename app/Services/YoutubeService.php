<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YoutubeService
{
    public function buscarMetadados(string $url): ?array
    {
        $videoId = $this->extrairVideoId($url);
        if (! $videoId) {
            return null;
        }

        $dados = $this->buscarOEmbed($url);
        if (! $dados) {
            return null;
        }

        // Tentar buscar duração via YouTube Data API (opcional, requer YOUTUBE_API_KEY)
        $duracaoSegundos = null;
        $apiKey = config('services.youtube.key');
        if ($apiKey) {
            $duracaoSegundos = $this->buscarDuracao($videoId, $apiKey);
        }

        return [
            'video_id'         => $videoId,
            'titulo'           => $dados['title'] ?? null,
            'canal'            => $dados['author_name'] ?? null,
            'thumbnail'        => "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg",
            'duracao_segundos' => $duracaoSegundos,
        ];
    }

    public function extrairVideoId(string $url): ?string
    {
        $patterns = [
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)([A-Za-z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    private function buscarOEmbed(string $url): ?array
    {
        try {
            $response = Http::timeout(5)->get('https://www.youtube.com/oembed', [
                'url'    => $url,
                'format' => 'json',
            ]);

            if ($response->ok()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::warning('YouTube oEmbed falhou', ['url' => $url, 'erro' => $e->getMessage()]);
        }

        return null;
    }

    private function buscarDuracao(string $videoId, string $apiKey): ?int
    {
        try {
            $response = Http::timeout(5)->get('https://www.googleapis.com/youtube/v3/videos', [
                'id'   => $videoId,
                'part' => 'contentDetails',
                'key'  => $apiKey,
            ]);

            if ($response->ok()) {
                $duracao = data_get($response->json(), 'items.0.contentDetails.duration');
                if ($duracao) {
                    return $this->iso8601ParaSegundos($duracao);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('YouTube API duração falhou', ['id' => $videoId, 'erro' => $e->getMessage()]);
        }

        return null;
    }

    private function iso8601ParaSegundos(string $duracao): int
    {
        // PT3M45S → 225 segundos
        preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?/', $duracao, $m);
        return (int) ($m[1] ?? 0) * 3600
            + (int) ($m[2] ?? 0) * 60
            + (int) ($m[3] ?? 0);
    }
}
