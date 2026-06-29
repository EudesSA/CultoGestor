<?php

namespace App\Http\Controllers;

use App\Exports\ParticipacaoCantoresExport;
use App\Models\AvaliacaoCulto;
use App\Models\Escala;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class RelatorioController extends Controller
{
    private function periodo(Request $request): array
    {
        $mes = (int) $request->integer('mes', now()->month);
        $ano = (int) $request->integer('ano', now()->year);
        $inicio = Carbon::create($ano, $mes, 1)->startOfMonth();

        return [$mes, $ano, $inicio, $inicio->copy()->endOfMonth()];
    }

    /** Escala mensal por função (PDF). */
    public function escalaMensal(Request $request): Response
    {
        [$mes, $ano, $inicio, $fim] = $this->periodo($request);

        $escalas = Escala::query()
            ->with(['culto.tipo', 'funcao', 'user'])
            ->whereHas('culto', fn ($q) => $q->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()]))
            ->get()
            ->sortBy(fn ($e) => $e->culto?->data)
            ->groupBy(fn ($e) => $e->funcao?->nome ?? 'Sem função');

        $pdf = Pdf::loadView('relatorios.escala-mensal', [
            'escalas' => $escalas,
            'periodo' => $inicio->translatedFormat('F Y'),
        ]);

        return $pdf->download("escala-{$ano}-{$mes}.pdf");
    }

    /** Participação por cantor (XLS). */
    public function participacaoCantores(Request $request): Response
    {
        [$mes, $ano] = $this->periodo($request);

        return Excel::download(
            new ParticipacaoCantoresExport($mes, $ano),
            "participacao-cantores-{$ano}-{$mes}.xlsx",
        );
    }

    /** Qualidade técnica — médias das avaliações do mês (PDF). */
    public function avaliacoesQualidade(Request $request): Response
    {
        [$mes, $ano, $inicio, $fim] = $this->periodo($request);

        $avaliacoes = AvaliacaoCulto::query()
            ->with(['culto.tipo', 'user'])
            ->whereHas('culto', fn ($q) => $q->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()]))
            ->get();

        $medias = [
            'geral'       => round((float) $avaliacoes->avg('nota_geral'), 1),
            'som'         => round((float) $avaliacoes->whereNotNull('nota_som')->avg('nota_som'), 1),
            'projecao'    => round((float) $avaliacoes->whereNotNull('nota_projecao')->avg('nota_projecao'), 1),
            'transmissao' => round((float) $avaliacoes->whereNotNull('nota_transmissao')->avg('nota_transmissao'), 1),
        ];

        $pdf = Pdf::loadView('relatorios.avaliacoes', [
            'avaliacoes' => $avaliacoes,
            'medias'     => $medias,
            'periodo'    => $inicio->translatedFormat('F Y'),
        ]);

        return $pdf->download("avaliacoes-{$ano}-{$mes}.pdf");
    }
}
