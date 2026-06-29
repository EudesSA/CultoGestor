<?php

namespace App\Exports;

use App\Models\HistoricoCantor;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Participação por cantor: nº de músicas cantadas no período,
 * a partir do log imutável historico_cantores.
 */
class ParticipacaoCantoresExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly int $mes,
        private readonly int $ano,
    ) {}

    public function collection()
    {
        $inicio = Carbon::create($this->ano, $this->mes, 1)->startOfMonth();
        $fim    = $inicio->copy()->endOfMonth();

        return HistoricoCantor::query()
            ->whereBetween('data_culto', [$inicio->toDateString(), $fim->toDateString()])
            ->selectRaw('cantor_id, COUNT(*) as total, MAX(data_culto) as ultima')
            ->with('cantor')
            ->groupBy('cantor_id')
            ->orderByDesc('total')
            ->get();
    }

    public function headings(): array
    {
        return ['Cantor', 'Músicas cantadas', 'Última participação'];
    }

    public function map($row): array
    {
        return [
            $row->cantor?->nome ?? '—',
            $row->total,
            $row->ultima ? Carbon::parse($row->ultima)->format('d/m/Y') : '—',
        ];
    }
}
