<?php

namespace App\Http\Controllers;

use App\Models\Escala;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConfirmacaoEscalaController extends Controller
{
    public function show(string $token): View
    {
        $escala = Escala::where('token_confirmacao', $token)
            ->with(['culto.tipo', 'funcao', 'user'])
            ->firstOrFail();

        return view('escalas.confirmacao', compact('escala', 'token'));
    }

    public function confirmar(string $token): RedirectResponse
    {
        $escala = Escala::where('token_confirmacao', $token)->firstOrFail();

        if ($escala->status !== 'confirmado') {
            $escala->update(['status' => 'confirmado', 'confirmado_em' => now()]);
        }

        return redirect()->route('escala.confirmacao', $token)
            ->with('mensagem', 'Presença confirmada! Obrigado.')
            ->with('tipo', 'sucesso');
    }

    public function recusar(string $token): RedirectResponse
    {
        $escala = Escala::where('token_confirmacao', $token)->firstOrFail();

        $escala->update(['status' => 'recusado', 'confirmado_em' => null]);

        return redirect()->route('escala.confirmacao', $token)
            ->with('mensagem', 'Participação recusada. Avisamos o responsável.')
            ->with('tipo', 'info');
    }
}
