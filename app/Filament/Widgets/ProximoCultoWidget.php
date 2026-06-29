<?php

namespace App\Filament\Widgets;

use App\Models\Culto;
use Filament\Widgets\Widget;

class ProximoCultoWidget extends Widget
{
    protected string $view = 'filament.widgets.proximo-culto';
    protected static ?int $sort   = 2;
    protected int | string | array $columnSpan = 'full';

    public function getCulto(): ?Culto
    {
        return Culto::with(['tipo', 'escalas.funcao', 'escalas.user', 'liturgias', 'musicas.cantor'])
            ->where('data', '>=', today())
            ->orderBy('data')
            ->first();
    }
}
