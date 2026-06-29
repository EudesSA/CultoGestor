<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo ?: 'Apresentação' }} — CultoGestor</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; background: #000; overflow: hidden; font-family: system-ui, sans-serif; }
        #palco { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; background: #000; }
        #palco img, #palco video, #palco iframe, #palco embed {
            max-width: 100%; max-height: 100%; width: 100%; height: 100%; border: 0; object-fit: contain; background: #000;
        }
        #palco img { object-fit: contain; }
        #audioBox { color: #fff; text-align: center; }
        #audioBox .nota { font-size: 6rem; margin-bottom: 1rem; }
        #audioBox h1 { font-size: 2rem; font-weight: 700; }

        /* Barra de controle */
        #barra {
            position: fixed; top: 0; left: 0; right: 0; z-index: 50;
            display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
            padding: .6rem .8rem; background: rgba(17,24,39,.92); backdrop-filter: blur(6px);
            color: #e5e7eb; transition: opacity .3s, transform .3s; font-size: .85rem;
        }
        #barra.oculta { opacity: 0; transform: translateY(-100%); pointer-events: none; }
        #barra .titulo { font-weight: 600; margin-right: auto; max-width: 40vw; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .btn {
            display: inline-flex; align-items: center; gap: .35rem; padding: .4rem .7rem; border-radius: .5rem;
            background: #374151; color: #fff; border: 1px solid #4b5563; cursor: pointer; font-size: .8rem; white-space: nowrap;
        }
        .btn:hover { background: #4b5563; }
        .btn.primary { background: #4f46e5; border-color: #6366f1; }
        .btn.primary:hover { background: #6366f1; }
        .btn.danger { background: #7f1d1d; border-color: #991b1b; }
        .sep { width: 1px; height: 20px; background: #4b5563; margin: 0 .25rem; }
        #telas { display: flex; gap: .4rem; flex-wrap: wrap; align-items: center; }
        #telasLabel { color: #9ca3af; font-size: .75rem; }
        #dica { color: #9ca3af; font-size: .72rem; }
        kbd { background: #1f2937; border: 1px solid #374151; border-radius: 4px; padding: 1px 5px; font-size: .7rem; }
    </style>
</head>
<body>
    <div id="barra">
        <span class="titulo">{{ $titulo ?: 'Apresentação' }}</span>

        <span id="telasLabel">Detectando monitores…</span>
        <div id="telas"></div>

        <div class="sep"></div>
        <button class="btn" onclick="telaCheia()">⛶ Tela cheia</button>
        <button class="btn danger" onclick="window.close()">✕ Fechar</button>
        <span id="dica" class="sep" style="background:none"><kbd>Esc</kbd> sai · <kbd>F</kbd> tela cheia</span>
    </div>

    <div id="palco">
        @if($tipo === 'youtube')
            <iframe
                src="https://www.youtube.com/embed/{{ $youtubeId }}?autoplay=1&rel=0&modestbranding=1"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                allowfullscreen></iframe>

        @elseif($tipo === 'video')
            <video src="{{ $src }}" controls autoplay></video>

        @elseif($tipo === 'imagem')
            <img src="{{ $src }}" alt="{{ $titulo }}">

        @elseif($tipo === 'pdf')
            <iframe src="{{ $src }}#toolbar=0&navpanes=0"></iframe>

        @elseif($tipo === 'audio')
            <div id="audioBox">
                <div class="nota">🎵</div>
                <h1>{{ $titulo ?: 'Áudio' }}</h1>
                <audio src="{{ $src }}" controls autoplay style="margin-top:1.5rem"></audio>
            </div>
        @endif
    </div>

    <script>
        const palco = document.documentElement;

        async function telaCheia(screen = null) {
            try {
                await palco.requestFullscreen(screen ? { screen } : {});
            } catch (e) {
                try { await palco.requestFullscreen(); } catch (_) {}
            }
        }

        // --- Detecção de monitores (Window Management API) ---
        async function detectarMonitores() {
            const telasEl = document.getElementById('telas');
            const label = document.getElementById('telasLabel');

            if (!('getScreenDetails' in window)) {
                label.textContent = 'Monitor único / navegador sem suporte';
                return;
            }

            let details;
            try {
                details = await window.getScreenDetails();
            } catch (e) {
                label.textContent = 'Permissão de monitores negada';
                return;
            }

            const render = () => {
                telasEl.innerHTML = '';
                const screens = details.screens;
                label.textContent = screens.length > 1 ? 'Apresentar em:' : 'Monitor:';
                screens.forEach((s, i) => {
                    const btn = document.createElement('button');
                    const nome = s.label || ('Tela ' + (i + 1));
                    btn.className = 'btn' + (s.isPrimary ? '' : ' primary');
                    btn.textContent = '🖥 ' + nome + (s.isPrimary ? ' (principal)' : '');
                    btn.title = `${s.width}×${s.height}`;
                    btn.onclick = () => telaCheia(s);
                    telasEl.appendChild(btn);
                });
            };

            render();
            details.addEventListener('screenschange', render);
        }

        // --- Auto-ocultar a barra ---
        const barra = document.getElementById('barra');
        let timer = null;
        function mostrarBarra() {
            barra.classList.remove('oculta');
            clearTimeout(timer);
            timer = setTimeout(() => barra.classList.add('oculta'), 3500);
        }
        document.addEventListener('mousemove', mostrarBarra);
        document.addEventListener('keydown', (e) => {
            mostrarBarra();
            if (e.key === 'f' || e.key === 'F') telaCheia();
        });

        detectarMonitores();
        mostrarBarra();
    </script>
</body>
</html>
