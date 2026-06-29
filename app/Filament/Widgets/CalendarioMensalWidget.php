<?php

namespace App\Filament\Widgets;

use App\Models\Culto;
use Filament\Notifications\Notification;
use Guava\Calendar\Filament\Actions\CreateAction;
use Guava\Calendar\Filament\Actions\ViewAction;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\DateClickInfo;
use Guava\Calendar\ValueObjects\EventClickInfo;
use Guava\Calendar\ValueObjects\EventDropInfo;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CalendarioMensalWidget extends CalendarWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected ?string $locale = 'pt-BR';

    protected bool $dateClickEnabled = true;

    protected bool $eventClickEnabled = true;

    protected bool $eventDragEnabled = true;

    /**
     * Opções do calendário (vkurko/Event Calendar): habilita alternância
     * entre visão mensal, semanal e lista, com botões em PT-BR.
     */
    public function getOptions(): array
    {
        return [
            'headerToolbar' => [
                'start'  => 'prev,next today',
                'center' => 'title',
                'end'    => 'dayGridMonth,timeGridWeek,listWeek',
            ],
            'buttonText' => [
                'today'        => 'Hoje',
                'dayGridMonth' => 'Mês',
                'timeGridWeek' => 'Semana',
                'listWeek'     => 'Lista',
            ],
            'editable'     => true,
            'firstDay'     => 0,
            'noEventsContent' => 'Nenhum culto neste período',
        ];
    }

    // Persisted across Livewire requests so fillForm can read it safely
    public ?string $dataSelecionada = null;

    protected function getEvents(FetchInfo $info): Collection|array
    {
        return Culto::with('tipo')
            ->whereDate('data', '>=', $info->start)
            ->whereDate('data', '<=', $info->end)
            ->orderBy('data')
            ->get();
    }

    /** Só os papéis com permissão de criar culto (policy Create:Culto). */
    protected function podeCriarCulto(): bool
    {
        return (bool) auth()->user()?->can('create', Culto::class);
    }

    public function getHeaderActions(): array
    {
        return [
            CreateAction::make('createCulto')
                ->model(Culto::class)
                ->modalHeading('Novo Culto')
                // Esconde o botão e barra a ação para quem não pode criar culto.
                ->visible(fn () => $this->podeCriarCulto())
                // Arrow function captures $this — avoids DateClickInfo type-hint
                // which triggers a CalendarAction null-context crash on re-render
                ->fillForm(fn () => [
                    'data' => $this->dataSelecionada ?? now()->toDateString(),
                ])
                ->after(fn () => $this->refreshRecords()),
        ];
    }

    protected function onDateClick(DateClickInfo $info): void
    {
        // Clicar num dia só abre o formulário de novo culto para quem tem permissão.
        if (! $this->podeCriarCulto()) {
            return;
        }

        $this->dataSelecionada = $info->date->toDateString();
        $this->mountAction('createCulto');
    }

    protected function onEventClick(EventClickInfo $info, Model $event, ?string $action = null): void
    {
        $this->mountAction('view');
    }

    /**
     * Arrastar e soltar reagenda o culto para a nova data (M1).
     * A alteração de `data` dispara o CultoObserver → sync no Google Calendar.
     */
    protected function onEventDrop(EventDropInfo $info, Model $event): bool
    {
        if (! $event instanceof Culto) {
            return false;
        }

        // Reagendar é uma edição: só quem pode atualizar o culto arrasta no calendário.
        if (! auth()->user()?->can('update', $event)) {
            $this->refreshRecords();

            Notification::make()
                ->title('Sem permissão')
                ->body('Você não pode reagendar cultos.')
                ->danger()
                ->send();

            return false;
        }

        $novaData = $info->event->getStart();
        $event->update(['data' => $novaData->toDateString()]);

        $this->refreshRecords();

        Notification::make()
            ->title('Culto reagendado')
            ->body(($event->tipo?->nome ?? 'Culto') . ' movido para ' . $novaData->format('d/m/Y'))
            ->success()
            ->send();

        return true;
    }

    public function viewAction(): ViewAction
    {
        return ViewAction::make()
            ->modalHeading(fn () => $this->getEventRecord()?->nomeCompleto ?? 'Detalhes do Culto')
            ->extraModalFooterActions([
                \Filament\Actions\Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(fn () => (bool) auth()->user()?->can('update', $this->getEventRecord() ?? Culto::class))
                    ->url(fn () => route('filament.admin.resources.cultos.edit', $this->getEventRecord()))
                    ->color('gray'),
            ]);
    }

    public function getHeading(): string
    {
        return 'Calendário de Programações';
    }
}
