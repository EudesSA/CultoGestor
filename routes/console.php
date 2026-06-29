<?php

use App\Jobs\MonitorarProvaiVedeJob;
use App\Jobs\NotificarEscalaJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Aviso automático 3 dias antes do culto para escalados pendentes (Módulo 1).
Schedule::job(new NotificarEscalaJob)
    ->dailyAt('08:00')
    ->name('notificar-escala-3-dias')
    ->withoutOverlapping();

// Scraper semanal do Provai e Vede → cria registros pendentes de aprovação (Módulo 4).
Schedule::job(new MonitorarProvaiVedeJob)
    ->weeklyOn(1, '06:00')
    ->name('scraper-provai-vede')
    ->withoutOverlapping();
