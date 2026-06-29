<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #222; }
        h1 { font-size: 18px; color: #1f3a5f; margin: 0 0 2px; }
        .sub { color: #666; font-size: 11px; margin-bottom: 14px; }
        .cards { width: 100%; margin-bottom: 16px; }
        .card { display: inline-block; width: 22%; background: #f0f4f8; border-radius: 6px; padding: 8px; margin-right: 1%; text-align: center; }
        .card .n { font-size: 22px; font-weight: bold; color: #2e6da4; }
        .card .l { font-size: 10px; color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 4px 6px; font-size: 11px; }
        th { background: #f0f4f8; color: #444; }
        tr:nth-child(even) td { background: #fafafa; }
    </style>
</head>
<body>
    <h1>Qualidade Técnica — Avaliações Pós-Culto</h1>
    <p class="sub">CultoGestor · {{ ucfirst($periodo) }} · {{ $avaliacoes->count() }} avaliação(ões)</p>

    <div class="cards">
        <div class="card"><div class="n">{{ $medias['geral'] ?: '—' }}</div><div class="l">Geral</div></div>
        <div class="card"><div class="n">{{ $medias['som'] ?: '—' }}</div><div class="l">Sonoplastia</div></div>
        <div class="card"><div class="n">{{ $medias['projecao'] ?: '—' }}</div><div class="l">Projeção</div></div>
        <div class="card"><div class="n">{{ $medias['transmissao'] ?: '—' }}</div><div class="l">Transmissão</div></div>
    </div>

    <table>
        <thead>
            <tr><th>Data</th><th>Culto</th><th>Avaliador</th><th>Geral</th><th>Som</th><th>Proj.</th><th>Transm.</th><th>Observações</th></tr>
        </thead>
        <tbody>
            @forelse($avaliacoes as $a)
            <tr>
                <td>{{ $a->culto?->data?->format('d/m/Y') ?? '—' }}</td>
                <td>{{ $a->culto?->tipo?->nome ?? 'Culto' }}</td>
                <td>{{ $a->user?->name ?? '—' }}</td>
                <td>{{ $a->nota_geral }}</td>
                <td>{{ $a->nota_som ?? '—' }}</td>
                <td>{{ $a->nota_projecao ?? '—' }}</td>
                <td>{{ $a->nota_transmissao ?? '—' }}</td>
                <td>{{ $a->observacoes }}</td>
            </tr>
            @empty
            <tr><td colspan="8">Nenhuma avaliação no período.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
