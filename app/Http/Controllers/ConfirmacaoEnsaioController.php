<?php

namespace App\Http\Controllers;

use App\Models\EnsaioParticipante;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConfirmacaoEnsaioController extends Controller
{
    public function show(string $token): View
    {
        $participante = EnsaioParticipante::where('token_confirmacao', $token)
            ->with(['ensaio.culto.tipo', 'ensaio.musicas', 'user'])
            ->firstOrFail();

        return view('ensaios.confirmacao', compact('participante', 'token'));
    }

    public function confirmar(string $token): RedirectResponse
    {
        $participante = EnsaioParticipante::where('token_confirmacao', $token)->firstOrFail();

        if ($participante->status !== 'confirmado') {
            $participante->update(['status' => 'confirmado', 'confirmado_em' => now()]);
        }

        return redirect()->route('ensaio.confirmacao', $token)
            ->with('mensagem', 'Presença no ensaio confirmada! Obrigado.')
            ->with('tipo', 'sucesso');
    }

    public function recusar(string $token): RedirectResponse
    {
        $participante = EnsaioParticipante::where('token_confirmacao', $token)->firstOrFail();

        $participante->update(['status' => 'recusado', 'confirmado_em' => null]);

        return redirect()->route('ensaio.confirmacao', $token)
            ->with('mensagem', 'Participação recusada. Avisamos o responsável.')
            ->with('tipo', 'info');
    }
}
