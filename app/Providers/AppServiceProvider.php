<?php

namespace App\Providers;

use App\Models\Culto;
use App\Models\EnsaioParticipante;
use App\Models\Escala;
use App\Models\Musica;
use App\Observers\CultoObserver;
use App\Observers\EnsaioParticipanteObserver;
use App\Observers\EscalaObserver;
use App\Observers\MusicaObserver;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Carbon::setLocale('pt_BR');

        // Em produção (HostGator com SSL) força HTTPS nas URLs geradas —
        // necessário para PWA, push e cookies seguros mesmo atrás de proxy.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Windows: garante o openssl.cnf p/ operações EC (chaves VAPID / Web Push),
        // que falham sem ele tanto na web quanto no queue worker.
        if (PHP_OS_FAMILY === 'Windows' && ! getenv('OPENSSL_CONF')) {
            $cnf = dirname(PHP_BINARY) . '\\extras\\ssl\\openssl.cnf';
            if (is_file($cnf)) {
                putenv('OPENSSL_CONF=' . $cnf);
                $_ENV['OPENSSL_CONF'] = $cnf;
            }
        }

        Culto::observe(CultoObserver::class);
        Escala::observe(EscalaObserver::class);
        Musica::observe(MusicaObserver::class);
        EnsaioParticipante::observe(EnsaioParticipanteObserver::class);
    }
}
