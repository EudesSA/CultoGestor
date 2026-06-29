<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #222; }
        h1 { font-size: 18px; color: #1f3a5f; margin: 0 0 2px; }
        .sub { color: #666; font-size: 11px; margin-bottom: 14px; }
        h2 { font-size: 13px; color: #2e6da4; margin: 14px 0 4px; border-bottom: 1px solid #ddd; padding-bottom: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        th, td { text-align: left; padding: 4px 6px; font-size: 11px; }
        th { background: #f0f4f8; color: #444; }
        tr:nth-child(even) td { background: #fafafa; }
        .status { font-weight: bold; }
        .ok { color: #16a34a; } .no { color: #dc2626; } .pend { color: #d97706; }
    </style>
</head>
<body>
    <h1>Escala Mensal por Função</h1>
    <p class="sub">CultoGestor · {{ ucfirst($periodo) }}</p>

    @forelse($escalas as $funcao => $itens)
        <h2>{{ $funcao }}</h2>
        <table>
            <thead>
                <tr><th>Data</th><th>Culto</th><th>Membro</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach($itens as $e)
                <tr>
                    <td>{{ $e->culto?->data?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $e->culto?->tipo?->nome ?? 'Culto' }}</td>
                    <td>{{ $e->user?->name ?? '—' }}</td>
                    <td class="status {{ $e->status === 'confirmado' ? 'ok' : ($e->status === 'recusado' ? 'no' : 'pend') }}">
                        {{ ucfirst($e->status) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <p>Nenhuma escala no período.</p>
    @endforelse
</body>
</html>
