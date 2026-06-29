<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ApresentacaoController extends Controller
{
    private const TIPOS = ['youtube', 'imagem', 'video', 'audio', 'pdf'];

    /**
     * Página de apresentação em tela cheia, com seletor de monitor.
     */
    public function show(Request $request)
    {
        $tipo   = $request->string('tipo')->toString();
        $src    = $request->string('src')->toString();
        $titulo = $request->string('titulo')->toString();

        abort_unless(in_array($tipo, self::TIPOS, true), 404);

        $youtubeId = null;
        if ($tipo === 'youtube') {
            preg_match('/(?:v=|youtu\.be\/|embed\/)([A-Za-z0-9_-]{11})/', $src, $m);
            $youtubeId = $m[1] ?? null;
            abort_if(! $youtubeId, 404, 'Link do YouTube inválido.');
        } else {
            // Arquivos só podem vir do próprio sistema (streaming interno).
            abort_unless(str_starts_with($src, url('/')), 403);
        }

        return view('apresentar', compact('tipo', 'src', 'titulo', 'youtubeId'));
    }

    /**
     * Faz o streaming inline de um arquivo da Media Library (disk cultos),
     * para ser exibido na página de apresentação. Protegido por auth.
     */
    public function midia(Media $media): BinaryFileResponse
    {
        $path = $media->getPath();
        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Content-Type' => $media->mime_type,
        ]);
    }
}
