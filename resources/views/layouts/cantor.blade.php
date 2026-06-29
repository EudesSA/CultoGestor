<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Portal do Cantor' }} — CultoGestor</title>

    {{-- PWA: instalável no celular do cantor --}}
    @isset($pwaToken)
    <link rel="manifest" href="{{ route('cantor.manifest', $pwaToken) }}">
    @endisset
    <meta name="theme-color" content="#1E3A8A">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Cantor">
    <link rel="apple-touch-icon" href="{{ url('/icons/icon-192.png') }}">
    <link rel="icon" type="image/png" href="{{ url('/icons/icon-192.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            50:  '#EFF6FF',
                            100: '#DBEAFE',
                            200: '#BFDBFE',
                            500: '#3B82F6',
                            600: '#2563EB',
                            700: '#1D4ED8',
                            800: '#1E40AF',
                            900: '#1E3A8A',
                            950: '#172554',
                        },
                        teal: {
                            50:  '#F0FDFA',
                            100: '#CCFBF1',
                            200: '#99F6E4',
                            400: '#2DD4BF',
                            500: '#14B8A6',
                            600: '#0D9488',
                            700: '#0F766E',
                            800: '#115E59',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @livewireStyles
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .tab-active { border-bottom: 2px solid #1E3A8A; color: #1E3A8A; font-weight: 600; }
        .tab-inactive { border-bottom: 2px solid transparent; color: #64748B; }
        .tab-inactive:hover { color: #1E3A8A; border-bottom-color: #BFDBFE; }
    </style>
</head>
<body class="min-h-full bg-slate-50 text-slate-900 antialiased">

    {{-- Header --}}
    <header class="bg-navy-900 shadow-lg">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-teal-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-sm leading-tight tracking-wide">CultoGestor</p>
                    <p class="text-blue-200 text-xs">Portal do Cantor</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-white text-sm font-semibold">{{ $cantorNome ?? '' }}</p>
                <p class="text-blue-300 text-xs">{{ $cantorVoz ?? '' }}</p>
            </div>
        </div>
    </header>

    {{-- Content --}}
    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-6 pb-16">
        {{ $slot }}
    </main>

    <footer class="border-t border-slate-200 py-5 text-center text-xs text-slate-400 bg-white mt-auto">
        <span class="font-medium text-navy-900">CultoGestor</span> &copy; {{ date('Y') }}
        &nbsp;·&nbsp; Gestão de cultos e equipes de louvor
    </footer>

    {{-- Botões flutuantes: instalar app + ativar notificações --}}
    <div class="fixed bottom-4 right-4 z-50 flex flex-col items-end gap-2">
        <button id="pwa-notify" type="button"
                class="hidden items-center gap-2 rounded-full bg-teal-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:bg-teal-700 transition-colors">
            🔔 Ativar notificações
        </button>
        <button id="pwa-install" type="button"
                class="hidden items-center gap-2 rounded-full bg-navy-900 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:bg-navy-700 transition-colors">
            ⬇ Instalar app
        </button>
    </div>

    <script>
        window.PWA_TOKEN = @json($pwaToken ?? null);
        window.VAPID_PUBLIC = @json(config('webpush.vapid.public_key'));
    </script>

    @livewireScripts

    <script>
        // Registra o service worker (necessário para instalar como app).
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }

        // Botão de instalação (Android/Chrome). No iOS: Compartilhar → Adicionar à Tela de Início.
        let promptInstalar = null;
        const btn = document.getElementById('pwa-install');
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            promptInstalar = e;
            if (btn) { btn.classList.remove('hidden'); btn.classList.add('flex'); }
        });
        if (btn) {
            btn.addEventListener('click', async () => {
                if (!promptInstalar) return;
                promptInstalar.prompt();
                await promptInstalar.userChoice;
                promptInstalar = null;
                btn.classList.add('hidden');
            });
        }
        window.addEventListener('appinstalled', () => {
            if (btn) btn.classList.add('hidden');
        });

        // --- Push notifications ---
        function b64ToUint8(base64) {
            const pad = '='.repeat((4 - base64.length % 4) % 4);
            const b64 = (base64 + pad).replace(/-/g, '+').replace(/_/g, '/');
            const raw = atob(b64);
            return Uint8Array.from([...raw].map((c) => c.charCodeAt(0)));
        }

        async function inscreverPush() {
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
            if (!window.VAPID_PUBLIC || !window.PWA_TOKEN) return;

            const reg = await navigator.serviceWorker.ready;
            let sub = await reg.pushManager.getSubscription();
            if (!sub) {
                sub = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: b64ToUint8(window.VAPID_PUBLIC),
                });
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            await fetch(`/cantor/${window.PWA_TOKEN}/push-subscribe`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', ...(token ? { 'X-CSRF-TOKEN': token } : {}) },
                body: JSON.stringify(sub.toJSON()),
            });
        }

        const btnNotify = document.getElementById('pwa-notify');
        function atualizarBotaoNotify() {
            if (!btnNotify || !('Notification' in window) || !window.VAPID_PUBLIC) return;
            if (Notification.permission === 'default') {
                btnNotify.classList.remove('hidden'); btnNotify.classList.add('flex');
            } else {
                btnNotify.classList.add('hidden');
            }
        }
        if (btnNotify) {
            btnNotify.addEventListener('click', async () => {
                const perm = await Notification.requestPermission();
                btnNotify.classList.add('hidden');
                if (perm === 'granted') inscreverPush().catch(() => {});
            });
        }
        window.addEventListener('load', () => {
            atualizarBotaoNotify();
            if ('Notification' in window && Notification.permission === 'granted') {
                inscreverPush().catch(() => {});
            }
        });
    </script>
</body>
</html>
