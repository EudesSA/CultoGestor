<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Relatorios extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static UnitEnum|string|null $navigationGroup = 'Relatórios';

    protected static ?string $navigationLabel = 'Relatórios';

    protected static ?string $title = 'Relatórios Gerenciais';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.relatorios';

    public int $mes;

    public int $ano;

    public function mount(): void
    {
        $this->mes = (int) now()->month;
        $this->ano = (int) now()->year;
    }

    public function getMesesProperty(): array
    {
        return [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
        ];
    }

    public function getAnosProperty(): array
    {
        $atual = (int) now()->year;

        return range($atual - 3, $atual + 1);
    }
}
