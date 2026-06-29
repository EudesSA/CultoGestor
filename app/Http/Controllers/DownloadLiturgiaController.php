<?php

namespace App\Http\Controllers;

use App\Models\LouvorjaExportacao;
use Illuminate\Support\Facades\Storage;

class DownloadLiturgiaController extends Controller
{
    public function download(int $id)
    {
        $exportacao = LouvorjaExportacao::with('culto.tipo')->findOrFail($id);

        abort_unless(
            Storage::disk('local')->exists($exportacao->path_arquivo),
            404,
            'Arquivo .ja não encontrado. Gere novamente.'
        );

        $tipoNome     = $exportacao->culto->tipo?->nome ? $exportacao->culto->tipo->nome . '-' : '';
        $dataStr      = $exportacao->culto->data?->format('d-m-Y') ?? (string) $exportacao->culto_id;
        $nomeDownload = "Liturgia-{$tipoNome}{$dataStr}.ja";

        return Storage::disk('local')->download($exportacao->path_arquivo, $nomeDownload);
    }
}
