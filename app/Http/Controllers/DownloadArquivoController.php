<?php

namespace App\Http\Controllers;

use App\Models\AnuncioMidia;
use App\Models\Cantor;
use App\Models\MusicaArquivo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DownloadArquivoController extends Controller
{
    /** Arquivos de música — requer login Filament */
    public function musica(int $id, string $modo = 'download')
    {
        abort_unless(Auth::check(), 403);

        $arquivo = MusicaArquivo::findOrFail($id);
        return $this->servir($arquivo->path_local, $arquivo->nome_original, $arquivo->mime_type, $modo);
    }

    /** Arquivos de mídia de anúncio — requer login Filament */
    public function midia(int $id, string $modo = 'download')
    {
        abort_unless(Auth::check(), 403);

        $midia = AnuncioMidia::findOrFail($id);
        return $this->servir($midia->path_local, $midia->nome_original, $midia->mime_type, $modo);
    }

    /** Arquivos de música via portal do cantor — valida pelo token */
    public function musicaPortal(string $token, int $id, string $modo = 'download')
    {
        $cantor  = Cantor::where('token_portal', $token)->firstOrFail();
        $arquivo = MusicaArquivo::whereHas('musica', fn ($q) => $q->where('cantor_id', $cantor->id))
            ->findOrFail($id);

        return $this->servir($arquivo->path_local, $arquivo->nome_original, $arquivo->mime_type, $modo);
    }

    private function servir(?string $path, ?string $nome, ?string $mime, string $modo)
    {
        abort_if(! $path, 404);
        abort_unless(Storage::disk('cultos')->exists($path), 404);

        $nomeArquivo = $nome ?: basename($path);
        $mimeType    = $mime ?: Storage::disk('cultos')->mimeType($path) ?: 'application/octet-stream';

        if ($modo === 'inline' && $this->podeExibirInline($mimeType)) {
            return response()->stream(
                fn () => fpassthru(Storage::disk('cultos')->readStream($path)),
                200,
                [
                    'Content-Type'        => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $nomeArquivo . '"',
                ]
            );
        }

        return Storage::disk('cultos')->download($path, $nomeArquivo);
    }

    private function podeExibirInline(string $mime): bool
    {
        return str_starts_with($mime, 'image/')
            || $mime === 'application/pdf'
            || str_starts_with($mime, 'audio/')
            || str_starts_with($mime, 'video/');
    }
}
