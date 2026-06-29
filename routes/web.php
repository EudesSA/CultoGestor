<?php

use App\Http\Controllers\ApresentacaoController;
use App\Http\Controllers\ConfirmacaoEnsaioController;
use App\Http\Controllers\ConfirmacaoEscalaController;
use App\Http\Controllers\DownloadArquivoController;
use App\Http\Controllers\DownloadLiturgiaController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\RelatorioController;
use App\Livewire\ModoCulto;
use App\Livewire\PortalCantor;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/cantor/{token}', PortalCantor::class)->name('cantor.portal');

// Manifest PWA do portal do cantor — start_url aponta para o portal do próprio cantor,
// para que ao "instalar" o app abra direto na conta dele.
Route::get('/cantor/{token}/manifest.webmanifest', function (string $token) {
    return response()->json([
        'name'             => 'Portal do Cantor — CultoGestor',
        'short_name'       => 'Cantor',
        'description'      => 'Envie suas músicas e confirme escalas.',
        'start_url'        => url("/cantor/{$token}"),
        'scope'            => url('/cantor/'),
        'display'          => 'standalone',
        'orientation'      => 'portrait',
        'background_color' => '#1E3A8A',
        'theme_color'      => '#1E3A8A',
        'lang'             => 'pt-BR',
        'icons'            => [
            ['src' => url('/icons/icon-192.png'), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ['src' => url('/icons/icon-512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
        ],
    ])->header('Content-Type', 'application/manifest+json');
})->name('cantor.manifest');

// Inscrição de push do PWA do cantor (identidade pelo token do portal).
Route::post('/cantor/{token}/push-subscribe', [PushSubscriptionController::class, 'subscribe'])->name('cantor.push.subscribe');
Route::post('/cantor/{token}/push-unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])->name('cantor.push.unsubscribe');

Route::get('/escala/{token}', [ConfirmacaoEscalaController::class, 'show'])->name('escala.confirmacao');
Route::post('/escala/{token}/confirmar', [ConfirmacaoEscalaController::class, 'confirmar'])->name('escala.confirmar');
Route::post('/escala/{token}/recusar', [ConfirmacaoEscalaController::class, 'recusar'])->name('escala.recusar');

Route::get('/ensaio/{token}', [ConfirmacaoEnsaioController::class, 'show'])->name('ensaio.confirmacao');
Route::post('/ensaio/{token}/confirmar', [ConfirmacaoEnsaioController::class, 'confirmar'])->name('ensaio.confirmar');
Route::post('/ensaio/{token}/recusar', [ConfirmacaoEnsaioController::class, 'recusar'])->name('ensaio.recusar');

// Download / visualização de arquivos (requer login Filament)
Route::middleware('auth')->group(function () {
    Route::get('/arquivo/musica/{id}/{modo?}', [DownloadArquivoController::class, 'musica'])
        ->name('arquivo.musica')
        ->where('modo', 'download|inline');

    Route::get('/arquivo/midia/{id}/{modo?}', [DownloadArquivoController::class, 'midia'])
        ->name('arquivo.midia')
        ->where('modo', 'download|inline');

    Route::get('/liturgia/download/{id}', [DownloadLiturgiaController::class, 'download'])
        ->name('liturgia.download');

    Route::get('/modo-culto/{culto}', ModoCulto::class)->name('culto.modo');

    // Apresentação em tela cheia + seletor de monitor
    Route::get('/apresentar', [ApresentacaoController::class, 'show'])->name('apresentar');
    Route::get('/apresentar/midia/{media}', [ApresentacaoController::class, 'midia'])->name('apresentar.midia');

    // Relatórios gerenciais (PDF/XLS)
    Route::get('/relatorios/escala-mensal', [RelatorioController::class, 'escalaMensal'])->name('relatorios.escala');
    Route::get('/relatorios/participacao-cantores', [RelatorioController::class, 'participacaoCantores'])->name('relatorios.participacao');
    Route::get('/relatorios/avaliacoes', [RelatorioController::class, 'avaliacoesQualidade'])->name('relatorios.avaliacoes');
});

// Download / visualização de arquivos via portal do cantor (token público)
Route::get('/cantor/{token}/arquivo/{id}/{modo?}', [DownloadArquivoController::class, 'musicaPortal'])
    ->name('arquivo.musica.portal')
    ->where('modo', 'download|inline');
