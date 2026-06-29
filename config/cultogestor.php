<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Caminho base do Louvor JA (máquina local Windows)
    |--------------------------------------------------------------------------
    | Caminho raiz onde o Louvor JA lê os arquivos de mídia na máquina local.
    | Deve coincidir com onde o Google Drive Desktop sincroniza os arquivos.
    | Configure LOUVORJA_BASE_PATH no .env — nunca altere aqui diretamente.
    */
    'louvorja_base_path' => env('LOUVORJA_BASE_PATH', 'C:\CultoGestor'),

    /*
    |--------------------------------------------------------------------------
    | Scraper Provai e Vede
    |--------------------------------------------------------------------------
    | Página de onde o MonitorarProvaiVedeJob extrai os vídeos do YouTube.
    | Os encontrados entram como `pendente_aprovacao` para revisão do admin.
    |
    | IMPORTANTE: o scraper lê feeds RSS (Atom) do YouTube — assim consegue o
    | título e a DATA de cada vídeo, e aplica os filtros:
    |   - só importa vídeos com data >= hoje (agendamentos/futuros);
    |   - ignora versões em "Libras".
    |
    | O canal do Provai e Vede usa playlists mensais. Configure os feeds RSS
    | das playlists do mês atual e dos próximos, SEPARADOS POR VÍRGULA:
    |   PROVAI_VEDE_SCRAPER_URL="https://www.youtube.com/feeds/videos.xml?playlist_id=ID_JUNHO,https://www.youtube.com/feeds/videos.xml?playlist_id=ID_JULHO"
    |
    | Para obter o playlist_id: abra a playlist no YouTube e copie o trecho
    | "list=PL..." da URL. Também aceita o feed do canal (?channel_id=...).
    */
    'provai_vede_url' => env('PROVAI_VEDE_SCRAPER_URL', 'https://www.adv.st/provaievede'),

];
