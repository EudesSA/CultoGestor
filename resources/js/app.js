import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Driver de broadcast escolhido por ambiente:
//  - local:     'reverb'  (servidor próprio, php artisan reverb:start)
//  - produção:  'pusher'  (HostGator compartilhado — usa Pusher hospedado)
const driver = import.meta.env.VITE_BROADCAST_DRIVER ?? 'reverb';

if (driver === 'pusher') {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        forceTLS: true,
        enabledTransports: ['ws', 'wss'],
    });
} else {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}
