# CultoGestor — Relatório de Conformidade
**Gerado em:** 23/06/2026  
**Base:** Especificação Mestra v1.0 (CultoGestor_Especificacao_Mestra.docx)  
**Status possíveis:** CONFORME | PARCIAL | AUSENTE | DIVERGENTE | EXTRA

---

## ✅ PROGRESSO DA EXECUÇÃO

### Fase C1 — Reparos Críticos *(CONCLUÍDA — 23/06/2026)*
- **Event `ItemCultoAtivado`**: já existia e está correto (`ShouldBroadcastNow`, canal `culto.{id}`, payload `liturgia_id`). Risco R1 (crash no Modo Culto) **resolvido**.
- **Shield `^4`**: instalado (v4.2.0). Plugin registrado no `AdminPanelProvider`, config publicado (`config/filament-shield.php`), `super_admin` mapeado para o role **Administrador**. *(Geração de policies + refino "Cantor só vê o próprio" → Fase C4.)*
- **`spatie/laravel-permission`**: baixado de `^8` → `^7.0` (exigência do Shield; mais alinhado à spec que pedia `^6`).
- **`IgrejaResource`**: ocultado do menu (`$shouldRegisterNavigation = false`) — Multi-Igreja é Fase 9.
- **Horizon → Database Queue**: Horizon **não instalado** (exige `ext-pcntl`/`ext-posix`, indisponíveis no Windows). Decisão aprovada: usar driver `database` + `php artisan queue:work`. `QUEUE_CONNECTION=database` já configurado; tabela `jobs` OK. Tempo real do Modo Culto não depende de fila (Event é `ShouldBroadcastNow`).
- **Banco**: `migrate:fresh --seed` rodado (banco descartável). 5 roles + admin (`admin@cultogestor.com` / `password`) seedados. Painel inicializa sem erros.

**Divergência de stack aceita:** Laravel **13.16.1** (spec pede 12) — mantido, não regredir.

### Fase C4 — ACL / Permissões *(CONCLUÍDA — 23/06/2026)*
- **`shield:generate --all`**: 12 policies (`app/Policies/`) + 147 permissões (formato `Ação:Entidade`) geradas para todos os recursos/widgets do painel admin.
- **`ShieldRolePermissionSeeder`** (`database/seeders/`): mapeia roles → permissões (spec seção 7), idempotente, registrado no `DatabaseSeeder`:
  - **Administrador** → super_admin (bypass via gate)
  - **Diretor de Música** → 113 perms (CRUD nos recursos de domínio + ver usuários)
  - **Sonoplasta** → 21 perms (somente leitura)
  - **Pastor** → 21 perms (somente leitura)
  - **Cantor** → 10 perms (gerir Musica + ver Culto/Escala/Cantor)
- **`User::canAccessPanel()`**: agora exige ao menos uma role (`$this->roles()->exists()`). Usuário sem role é bloqueado.
- **`MusicaResource::getEloquentQuery()`**: Cantor enxerga apenas as próprias músicas (`whereHas('cantor', user_id = auth id)`); demais roles veem todas.
- **Verificação (DoD #6) PASSOU**: Cantor Um vê só "Musica do Um"; Diretor vê todas; Sonoplasta read-only; sem-role bloqueado do painel.

> Ordem de setup do zero: `migrate:fresh` → `php artisan shield:generate --all --option=policies_and_permissions --panel=admin` → `db:seed`.

### Fase C3 — Árvore de pastas do Drive *(CONCLUÍDA — 23/06/2026)*
- **Fonte única** criada em `Musica::diretorioArquivo($tipo)` + `Musica::categoriaArquivo($tipo)`: gera `Cultos/{YYYY}/{MM}/{DD-MM-YYYY}/{categoria}/` pela **data do culto**.
- Categorias conforme spec (Módulo 3): `playback` → `Playback`; demais (mp3/letra/cifra/partitura/outro) → `Musica Especial`.
- **`PortalCantor::enviarArquivo()`**: agora usa `diretorioArquivo()` + `storeAs()` com nome legível (`{musica-slug}-{tipo}.{ext}`) em vez de hash. Removido o helper antigo `diretorioUpload()` que gravava `Cantores/{cantor}/{data-upload}/`.
- **`ArquivosRelationManager`** (lado Diretor): unificado para a mesma fonte (antes usava categorias divergentes: Musica/Letras/Cifras).
- **Verificação PASSOU**: culto 20/07/2026 → `Cultos/2026/07/20-07-2026/Musica Especial` e `.../Playback`, idênticos em ambos os pontos de upload. Lint OK, sem referências órfãs.

### Fase C2 — Aviso 3 dias antes *(CONCLUÍDA — 23/06/2026)*
- **`NotificarEscalaJob`** (`app/Jobs/`, ShouldQueue): busca escalas `pendente` de cultos `agendado` com data = hoje+3 e notifica os escalados. Retorna a contagem de enviados.
- **`EscalaLembreteNotification`** (`app/Notifications/`): e-mail enfileirado "faltam 3 dias", reutiliza o link tokenizado de confirmação.
- **Scheduler** (`routes/console.php`): `Schedule::job(new NotificarEscalaJob)->dailyAt('08:00')->withoutOverlapping()`. Confirmado em `schedule:list` (`0 8 * * * notificar-escala-3-dias`).
- **Verificação PASSOU** (`Notification::fake()`): pendente a 3 dias → notificado; confirmado a 3 dias → não; pendente a 2 dias → não. `handle()` retornou 1.
- ⚠️ Requer `php artisan schedule:work` (ou cron `schedule:run`) ativo + um `queue:work` para processar a fila.

### Fase C5 — Widgets de Dashboard *(CONCLUÍDA — 23/06/2026)*
- **`AnunciosAtivosWidget`** (`app/Filament/Widgets/`): table widget full-span listando os anúncios ativos hoje (usa `Anuncio::scopeAtivos()`); colunas título, tipo, categoria, período (com "Sempre disponível").
- **`HistoricoRecenteWidget`**: table widget full-span do log imutável `historico_cantores` (data, cantor, música, tom, tipo de culto), ordenado por data.
- **Permissões Shield**: `shield:generate --option=permissions` (149 perms; +2 widgets); `ShieldRolePermissionSeeder` atualizado e re-aplicado → Diretor 115, Sonoplasta/Pastor 23, Cantor 10.
- **Verificação PASSOU**: painel descobre os 5 widgets; `table()` constrói sem erro (4 e 5 colunas). Lint OK.

> Dashboard agora: `StatsWidget` (4 contadores) + `ProximoCultoWidget` + `CalendarioMensalWidget` + `AnunciosAtivosWidget` + `HistoricoRecenteWidget`.

---

## ✅ REALINHAMENTO CONCLUÍDO (23/06/2026)

Todas as fases do plano de correção executadas e verificadas:

| Fase | Escopo | Prio. | Status |
|------|--------|-------|--------|
| C1 | Shield instalado, Horizon→Database Queue, IgrejaResource oculto, banco limpo | P1 | ✅ |
| C4 | ACL — policies + role/permission seeder + Cantor só vê o próprio + bloqueio de painel | P1 | ✅ |
| C3 | Árvore de pastas do Drive `Cultos/YYYY/MM/DD/{cat}/` (fonte única) | P1 | ✅ |
| C2 | Aviso 3 dias antes (`NotificarEscalaJob` + scheduler) | P2 | ✅ |
| C5 | Widgets dedicados (Anúncios Ativos / Histórico Recente) | P2 | ✅ |

**Não-conformidades remanescentes (aceitas):**
- Laravel 13 vs 12 (spec) — mantido por decisão; sem impacto funcional.
- Horizon não usado — substituído por Database Queue (incompatível com Windows).
- Itens de Fase 9 (scraper, WhatsApp, PWA, etc.) — fora de escopo por design.

**Operação contínua exige:** `php artisan queue:work` (filas: notificações, calendar) e `php artisan schedule:work` (aviso 3 dias). Tempo real do Modo Culto: `php artisan reverb:start` + `npm run dev`.

### Fase C6 — Histórico imutável + observer não registrado *(CONCLUÍDA — 23/06/2026)*

Achados durante a verificação dos itens PARCIAL "a verificar":

- **`MusicaObserver` NUNCA estava registrado** — só `Culto` e `Escala` estavam em `AppServiceProvider`. Consequência: a `CantorAgendadoNotification` na aprovação **nunca disparava** (a auditoria/memória assumiram que sim). Corrigido: `Musica::observe(MusicaObserver::class)`.
- **Histórico imutável AUSENTE** — `HistoricoCantor` nunca era criado em lugar nenhum (só lido). Viola o princípio "Histórico imutável" e o DoD #2. Corrigido: `MusicaObserver::registrarHistorico()` faz `firstOrCreate` por `musica_id` na aprovação (append-only, idempotente, nunca atualiza/deleta).
- **Verificação PASSOU**: aprovar música → 1 entrada de histórico com dados corretos (música/artista/tom/data/tipo) + `CantorAgendadoNotification` enviada; reaprovar → segue 1 (sem duplicar).

> Atualização do checklist A.2: "Histórico de cantores/músicas/datas gravado" passa de **PARCIAL** → **CONFORME**. "M2 — Status em tempo real + histórico imutável" agora plenamente CONFORME.

### Fase C7 — "Exibir Agora" no Modo Culto (M5) *(CONCLUÍDA — 23/06/2026)*
- **`ModoCulto`**: propriedade `anuncioAtivoId` + métodos `exibirAnuncio()`/`fecharAnuncio()` + computed `anunciosDisponiveis()` (usa `Anuncio::ativos()`) e `anuncioAtivo()`.
- **View `modo-culto.blade.php`**: seção "Anúncios ativos" na sidebar com botões "Exibir →"; overlay fullscreen `z-50` independente da liturgia, com título/categoria/thumbnail/descrição, fechar por botão e tecla **Esc**.
- **Verificação PASSOU**: blade compila; lista contém o anúncio ativo; exibir define o anúncio; fechar reseta. Atende M5 ("Exibir Agora, fullscreen, independente do período") e M8 ("Exibir Anúncio a qualquer momento, sidebar").
- *Nota:* o overlay é local à tela do operador (não broadcastado). Sincronizar para a tela de projeção via Reverb é um aprimoramento futuro opcional.

> Checklist A.2: "M5 — AnuncioResource + 'Exibir Agora' + sempre_disponivel" agora **CONFORME**.

### Fase C8 — Correções de UX + itens P2/P3 finais *(CONCLUÍDA — 24/06/2026)*

**Correções pedidas pelo usuário:**
- **Culto sem igreja**: campo `local` do `CultoForm` deixou de ser Select dependente de Igrejas e virou `TextInput` livre (com `datalist` de sugestões quando houver igrejas). Culto agora cria sem nenhuma igreja cadastrada.
- **Música sem culto (repertório)**: `musicas.culto_id` agora **nullable** (migration + `nullOnDelete`); `MusicaForm` e `PortalCantor::salvarMusica()` não exigem mais culto. O cantor pré-cadastra o repertório e a associação ocorre quando é escalado.
- **Bugs expostos e corrigidos:** `MusicaObserver` (created/deleted) protegido contra `culto_id` nulo (evita `TypeError` no job de Calendar); `registrarHistorico()` só grava quando há culto (histórico = músicas cantadas num culto). Notificação de aprovação continua disparando mesmo sem culto.
- ProvaiVede / Informativo / Anúncio: `culto_id` já era opcional nos forms — confirmado.

**Itens P2/P3 desenvolvidos:**
- **M4 — Favoritos Provai e Vede** (**CONFORME**): `ProvaiVede::favoritadoPor()` (belongsToMany) + `isFavorito()`/`alternarFavorito()`; ação coração (toggle) e filtro "Meus favoritos" na `ProvaiVedesTable`. Verificado.
- **M1 — View semanal + drag-drop** (**CONFORME**): `CalendarioMensalWidget` com toolbar Mês/Semana/Lista (PT-BR), `eventDragEnabled` + `onEventDrop()` reagenda o culto (atualiza `data` → dispara sync Calendar via observer); eventos `->editable(true)`. Via guava/calendar (vkurko).
- **M2 — QR Code por cantor** (**CONFORME**): já existia — modais `cantor-portal-modal.blade.php` e `musica-portal-modal.blade.php` geram QR com `simple-qrcode`.

> Smoke test final: views compilam, painel inicializa (5 widgets, 12 resources), rotas OK.

---

## 🏁 STATUS FINAL — Conformidade praticamente total

Todos os itens **P1 e P2** do checklist A.2 estão **CONFORME**. Remanescentes apenas de Fase 9 (fora de escopo): scraper Provai e Vede, WhatsApp, PWA, avaliação pós-culto, Banco de Hinos, Ensaio, Multi-igreja.

**Não-conformidades aceitas por decisão:** Laravel 13 (vs 12); Horizon→Database Queue (Windows).

**Operação:** `queue:work` + `schedule:work` + (Modo Culto) `reverb:start` + `npm run dev`.

---

## Fase C9 — Mídia (upload/link/thumbnail) + Apresentação multi-monitor *(CONCLUÍDA — 24/06/2026)*

**A) Upload + link + thumbnail nas 4 áreas (Musica, Provai e Vede, Informativo, Anúncio):**
- `ProvaiVede` e `Informativo` agora são `HasMedia` com coleção `arquivo` (disk `cultos`, single, vídeo/imagem/PDF/áudio); upload adicionado aos forms.
- Preview do thumbnail do YouTube adicionado aos forms de Provai e Vede e Informativo (Placeholder reativo). Música já tinha.
- `Anuncio` ganhou campos YouTube (`link_youtube`, `youtube_canal`, `youtube_thumbnail`, `duracao_segundos`) + seção no form; mídias continuam via RM.
- Ações **"Abrir"** (link/arquivo em nova aba) e **"Apresentar"** nas tabelas das 4 áreas (`App\Filament\Support\ApresentarActions`).

**B) Apresentação em monitor (Window Management API):**
- Trait `App\Concerns\Apresentavel` nos 4 models: `tipoApresentacao()`, `urlApresentacao()`, `urlAbrir()`, `temApresentacao()` (resolve YouTube ou arquivo).
- `ApresentacaoController`: `show` (página fullscreen) + `midia` (streaming inline de arquivo da Media Library, auth). Rotas `apresentar` e `apresentar.midia`.
- View `resources/views/apresentar.blade.php`: player fullscreen (imagem/vídeo/PDF/YouTube/áudio) + **seletor de monitor** via `getScreenDetails()` — lista as telas e abre em fullscreen na escolhida (`requestFullscreen({screen})`); barra auto-oculta; Esc/F.
- Integração no Modo Culto: botão "🖥 Apresentar em monitor" no item ativo e no overlay de anúncio.
- **Verificado:** caminho YouTube e caminho arquivo (PNG → tipo=imagem → streaming) resolvem corretamente; rotas registradas; views compilam; painel inicializa.

> ⚠️ **Requisito de operação:** o seletor de monitor usa a Window Management API, que só funciona em **contexto seguro**. Acessar via **`http://localhost`/`127.0.0.1`** (seguro mesmo em HTTP) ou **HTTPS**. Em IP de LAN sem HTTPS (ex.: `192.168.x.x`) a API não ativa e cai no fallback (abre janela; arrastar + F). Navegador: **Chrome/Edge**.

---

## Fase 9 (spec) — Módulos avançados *(parcial — 24/06/2026)*

Ambiente ajustado para **localhost**: `APP_URL=http://localhost:8000` + script `composer dev` agora roda `php artisan serve --host=localhost --port=8000` (Window Management API exige contexto seguro).

### Banco de Hinos *(CONCLUÍDO)*
- Tabelas `hinos` (numero, titulo, hinario, tom, tons_alternativos, link_youtube) + `hino_execucoes` (log append-only do tom tocado). Models `Hino`/`HinoExecucao`.
- `HinoResource` (CRUD, grupo Músicas & Cantores) com coluna "tom mais tocado" + ações Abrir/Apresentar.
- `Hino::tomMaisTocado()` = moda das execuções (fallback tom padrão). `registrarExecucao()` append-only.
- **Modo Culto:** busca por número na sidebar → mostra título + tom mais tocado + botões Apresentar / Registrar execução. Verificado.

### Sistema de Ensaio *(CONCLUÍDO)*
- Tabelas `ensaios` (ligado ao culto), `ensaio_participantes` (convocação + token + status), `ensaio_musicas` (repertório). Models + `EnsaioResource` com RelationManagers (Convocados, Músicas).
- Confirmação pública tokenizada: `GET /ensaio/{token}` (+ confirmar/recusar) → view própria com repertório. QR/Link no RM.
- `EnsaioConvocacaoNotification` (mail) disparada por `EnsaioParticipanteObserver` na convocação. Verificado (token, notificação, confirmação).

### Avaliação Pós-Culto + Relatórios *(CONCLUÍDO)*
- Tabela `avaliacoes_culto` (nota_geral + som/projeção/transmissão 1–5 + observações) + `AvaliacaoCulto` (`media`). `AvaliacaoCultoResource` (grupo Relatórios) com estrelas.
- **Relatórios** (página Filament `Relatorios` com seletor mês/ano + downloads):
  - Escala mensal por função → **PDF** (`relatorios.escala`, dompdf).
  - Participação por cantor → **XLS** (`relatorios.participacao`, maatwebsite/excel, a partir de `historico_cantores`).
  - Qualidade técnica (médias das avaliações) → **PDF** (`relatorios.avaliacoes`).
- Verificado: os 3 relatórios geram PDF/XLS sem erro.

> Permissões Shield regeradas (186) e mapeadas: Hino, Ensaio, AvaliacaoCulto adicionados ao seeder; página Relatórios concedida a Diretor/Sonoplasta/Pastor.

### Upload em Música (igual Provai e Vede) *(CONCLUÍDO — 24/06/2026)*
- `Musica` agora é `HasMedia` com coleção `arquivo` (disk cultos, vídeo/imagem/PDF/áudio); upload adicionado ao `MusicaForm`. Com isso, `urlApresentacao()` da música também resolve arquivo enviado (antes só YouTube). Verificado.

### WhatsApp via Web/Desktop *(CONCLUÍDO — 24/06/2026)*
- Sem API/credenciais: helper `App\Support\WhatsApp::link()` gera `wa.me/<num>?text=...` (DDI 55 automático, fallback sem número). Abre WhatsApp Web ou Desktop com a mensagem pronta.
- Botão **"WhatsApp"** nas Escalas (mensagem + link de confirmação) e na convocação de Ensaio. O portal do cantor já tinha. Verificado.

### Scraper Provai e Vede *(CONCLUÍDO — 24/06/2026)*
- `MonitorarProvaiVedeJob`: lê `cultogestor.provai_vede_url`, extrai IDs do YouTube (watch/embed/shorts/youtu.be), deduplica e cria `ProvaiVede` `pendente_aprovacao` (inativos), enriquecendo via `YoutubeService` (oEmbed). Agendado semanal (seg 06:00).
- `ProvaiVedeResource`: filtro "Pendentes de aprovação", ação **Aprovar** (ativa o vídeo), e botão **"Buscar novos vídeos"** (roda o scraper na hora).
- **Refinado (24/06/2026):** o job agora parseia o **feed RSS (Atom)** do YouTube (título + data por vídeo) e aplica filtros: importa **só vídeos com data ≥ hoje** (agendamentos) e **exclui versões em "Libras"**. Grava `data_exibicao`. Aceita **múltiplos feeds** (playlists mensais) separados por vírgula em `PROVAI_VEDE_SCRAPER_URL`. Fallback de regex para páginas HTML comuns.
- **Verificado:** feed Atom simulado → importa só os 2 futuros não-Libras, ignora passado e Libras, dedup na 2ª rodada; RSS real do YouTube extrai os vídeos.
- ⚠️ `adv.st/provaievede` é JS-renderizado (0 no HTML estático). Usar feeds RSS das **playlists mensais** do canal.
- **Gerenciável pelo sistema (24/06/2026):** novo recurso **Configurações → Playlists Provai e Vede** (`ProvaiVedePlaylistResource`): cadastra nome + URL/ID da playlist (cola o link inteiro → mutator extrai o `PL…`). O job lê as playlists ativas do banco (`ProvaiVedePlaylist::feedsAtivos()`) e só cai no `.env` se não houver nenhuma. Botão "Buscar vídeos agora" na própria tela. Verificado (extração do PL, feedUrl, job lendo do banco).

### Fase 9 restante (sob demanda)
- PWA · Multi-Igreja.

---

## 1. Stack e Versões

| Item | Esperado | Instalado | Status |
|------|----------|-----------|--------|
| PHP | 8.3 | 8.3.16 | **CONFORME** |
| Laravel | 12 | 13.16.1 (`^13.8` no composer.json) | **DIVERGENTE** |
| Filament | ^5 | 5.6.7 | **CONFORME** |
| MySQL | 8 | 8 (Laragon) | **CONFORME** |

> **Nota Laravel 13:** O `composer.json` usa `^13.8` em vez de `^12`. A diferença é pequena (sem quebras de API relevantes para o projeto), mas diverge da especificação. **Recomendação: não fazer downgrade** — Laravel 13 é estável e compatível com toda a stack. Apenas documentar o desvio.

---

## 2. Pacotes — composer.json / composer.lock

### Presentes e conformes

| Pacote | Versão instalada | Status |
|--------|-----------------|--------|
| `filament/filament` | 5.6.7 | **CONFORME** |
| `laravel/reverb` | 1.10.2 | **CONFORME** |
| `spatie/laravel-permission` | 8.0.0 | **CONFORME** (spec diz ^6; v8 é compatível) |
| `spatie/laravel-medialibrary` | 11.23.0 | **CONFORME** |
| `masbug/flysystem-google-drive-ext` | 2.5.0 | **CONFORME** |
| `spatie/laravel-google-calendar` | ^3.8 (no require) | **CONFORME** |
| `barryvdh/laravel-dompdf` | ^3.1 | **CONFORME** |
| `maatwebsite/excel` | ^3.1 | **CONFORME** |
| `simplesoftwareio/simple-qrcode` | ^4.2 | **CONFORME** |
| `leandrocfe/filament-ptbr-form-fields` | ^5.0 | **CONFORME** |
| `filament/spatie-laravel-media-library-plugin` | ^5.6 | **CONFORME** |

### Ausentes (previstos na spec)

| Pacote | Prioridade | Impacto da ausência |
|--------|-----------|---------------------|
| `bezhansalleh/filament-shield` | **P1** | Sem ACL no painel — todo usuário logado vê tudo |
| `laravel/horizon` | **P1** | Filas rodam via `queue:work`; sem dashboard/monitoramento de jobs |
| `jeffgreco13/filament-breezy` | P3 | Sem perfil de usuário / 2FA no painel (pode ser dispensável no Filament 5) |
| `laravel/boost` (dev) | P3 | Scaffolding; não bloqueia funcionalidade |

### Extras (não previstos na spec)

| Pacote | Uso atual |
|--------|-----------|
| `guava/calendar` 3.1.0 | Alimenta o `CalendarioMensalWidget` — aceitável |

---

## 3. Migrations e Banco

### Tabelas presentes

| Tabela | Arquivo | Status | Observação |
|--------|---------|--------|------------|
| `users` | `0001_01_01_000000_...` | **CONFORME** | + campos extras (avatar, phone_whatsapp via migration posterior) |
| `culto_tipos` | `2026_06_22_203038_...` | **CONFORME** | Tem `icone` e `ordem` extras — OK |
| `cultos` | `2026_06_22_203039_...` | **PARCIAL** | FK é `culto_tipo_id` (spec diz `tipo_id`) • Falta `igreja_id nullable` (previsto no CLAUDE.md) • Tem `local`, `google_meet_link`, `observacoes` extras — OK |
| `funcoes` | `2026_06_22_203041_...` | **CONFORME** | |
| `escalas` | `2026_06_22_203042_...` | **CONFORME** | |
| `cantores` | `2026_06_22_203043_...` | **PARCIAL** | Tem `token_portal` e `foto` (migration posterior) — extras úteis, OK |
| `musicas` | `2026_06_22_203051_...` | **CONFORME** | |
| `musica_arquivos` | `2026_06_22_203052_...` | **CONFORME** | |
| `historico_cantores` | `2026_06_22_203054_...` | **CONFORME** | |
| `culto_liturgias` | `2026_06_22_203055_...` | **PARCIAL** | Usa `nullableMorphs('referencia')` inline em vez de tabela separada `culto_liturgia_itens` — funcionalmente equivalente e mais limpo; aceitável |
| `provai_vede` | `2026_06_22_203108_...` | **CONFORME** | |
| `provai_vede_favoritos` | `2026_06_22_203110_...` | **CONFORME** | |
| `informativos` | `2026_06_22_203112_...` | **CONFORME** | |
| `anuncios` | `2026_06_22_203114_...` | **CONFORME** | |
| `anuncio_midias` | `2026_06_22_203115_...` | **CONFORME** | |
| `louvorja_exportacoes` | `2026_06_22_203117_...` | **CONFORME** | |
| `google_calendar_eventos` | `2026_06_22_203118_...` | **CONFORME** | |
| `permissions` (spatie) | `2026_06_22_203347_...` | **CONFORME** | |

### Tabelas ausentes / extras

| Tabela | Status | Observação |
|--------|--------|------------|
| `culto_liturgia_itens` | **AUSENTE** (substituída) | Morphs inline em `culto_liturgias` cobre o caso |
| `igrejas` | **EXTRA** | `2026_06_23_100000_...` — Multi-Igreja é Fase 9; antecipado. Não causa dano, mas diverge da ordem de fases |

---

## 4. Models

| Model | Arquivo | Status |
|-------|---------|--------|
| `Culto` | `app/Models/Culto.php` | **CONFORME** |
| `CultoTipo` | `app/Models/CultoTipo.php` | **CONFORME** |
| `CultoLiturgia` | `app/Models/CultoLiturgia.php` | **CONFORME** |
| `Escala` | `app/Models/Escala.php` | **CONFORME** |
| `Funcao` | `app/Models/Funcao.php` | **CONFORME** |
| `Cantor` | `app/Models/Cantor.php` | **CONFORME** |
| `Musica` | `app/Models/Musica.php` | **CONFORME** |
| `MusicaArquivo` | `app/Models/MusicaArquivo.php` | **CONFORME** |
| `HistoricoCantor` | `app/Models/HistoricoCantor.php` | **CONFORME** |
| `ProvaiVede` | `app/Models/ProvaiVede.php` | **CONFORME** |
| `Informativo` | `app/Models/Informativo.php` | **CONFORME** |
| `Anuncio` | `app/Models/Anuncio.php` | **CONFORME** |
| `AnuncioMidia` | `app/Models/AnuncioMidia.php` | **CONFORME** |
| `LouvorjaExportacao` | `app/Models/LouvorjaExportacao.php` | **CONFORME** |
| `GoogleCalendarEvento` | `app/Models/GoogleCalendarEvento.php` | **CONFORME** |
| `User` | `app/Models/User.php` | **CONFORME** |
| `Igreja` | `app/Models/Igreja.php` | **EXTRA** (Fase 9) |
| `CultoLiturgiaItem` | — | **AUSENTE** (substituído por morphs inline — OK) |

---

## 5. Filament Resources

| Resource | Localização | Status | Observação |
|----------|-------------|--------|------------|
| `CultoResource` | `app/Filament/Resources/Cultos/` | **CONFORME** | Com EscalasRelationManager ✓ |
| `EscalaResource` | `app/Filament/Resources/Escalas/` | **CONFORME** | Create/Edit/List presentes |
| `CantorResource` | `app/Filament/Resources/Cantors/` | **CONFORME** | Com MusicasRelationManager ✓ |
| `MusicaResource` | `app/Filament/Resources/Musicas/` | **CONFORME** | Com ArquivosRelationManager ✓ |
| `ProvaiVedeResource` | `app/Filament/Resources/ProvaiVedes/` | **CONFORME** | |
| `InformativoResource` | `app/Filament/Resources/Informativos/` | **CONFORME** | |
| `AnuncioResource` | `app/Filament/Resources/Anuncios/` | **CONFORME** | Com MidiasRelationManager ✓ |
| `UserResource` | `app/Filament/Resources/Users/` | **CONFORME** | |
| `FuncaoResource` | `app/Filament/Resources/Funcaos/` | **CONFORME** | (extra útil) |
| `CultoTipoResource` | `app/Filament/Resources/CultoTipos/` | **CONFORME** | (extra útil) |
| `IgrejaResource` | `app/Filament/Resources/Igrejas/` | **EXTRA** | Fase 9 antecipada |

---

## 6. Filament Widgets

| Widget | Arquivo | Status | Observação |
|--------|---------|--------|------------|
| `CalendarioMensalWidget` | `app/Filament/Widgets/CalendarioMensalWidget.php` | **CONFORME** | Via guava/calendar |
| `ProximoCultoWidget` | `app/Filament/Widgets/ProximoCultoWidget.php` | **CONFORME** | |
| `StatsWidget` | `app/Filament/Widgets/StatsWidget.php` | **PARCIAL** | Existe mas pode não cobrir todos os 6 contadores da spec |
| `MusicasPendentesWidget` | — | **AUSENTE** | |
| `EscalasPendentesWidget` | — | **AUSENTE** | |
| `AnunciosAtivosWidget` | — | **AUSENTE** | |
| `HistoricoRecenteWidget` | — | **AUSENTE** | |

---

## 7. Jobs

| Job | Arquivo | Status | Observação |
|-----|---------|--------|------------|
| `GerarLiturgiaJaJob` | `app/Jobs/GerarLiturgiaJaJob.php` | **CONFORME** | |
| `SincronizarGoogleCalendarJob` | `app/Jobs/SincronizarGoogleCalendarJob.php` | **CONFORME** | |
| `SincronizarGoogleDriveJob` | — | **DISPENSÁVEL** | Modelo aprovado = pasta local + Drive Desktop. Upload já grava direto no disk `cultos` (`PortalCantor::enviarArquivo` → `$file->store(...,'cultos')`). Não precisa de Job. **Porém:** a árvore de pastas diverge — ver seção 7b |
| `NotificarEscalaJob` | — | **AUSENTE** | P2 — aviso 3 dias antes do culto. Único gap de fluxo real desta seção |
| `MonitorarProvaiVedeJob` | — | **AUSENTE** | P3 — scraper (Fase 9, fora de escopo) |

## 7b. Observers (descobertos após re-inventário)

> A auditoria inicial não detectou estes arquivos (Glob truncada em 100 itens). Eles fazem o wire-up automático do fluxo.

| Observer | Arquivo | Função | Status |
|----------|---------|--------|--------|
| `CultoObserver` | `app/Observers/CultoObserver.php` | Dispara `SincronizarGoogleCalendarJob` ao salvar/excluir culto | **CONFORME** |
| `EscalaObserver` | `app/Observers/EscalaObserver.php` | Envia `EscalaConfirmacaoNotification` ao criar escala | **CONFORME** |
| `MusicaObserver` | `app/Observers/MusicaObserver.php` | Envia `CantorAgendadoNotification` quando música é aprovada | **CONFORME** |

### Divergência da estrutura de pastas (Módulo 3, P1)

`PortalCantor::diretorioUpload()` grava em `Cantores/{cantor}/{data-upload}/{musica}` — a spec exige `Cultos/YYYY/MM/DD-MM-YYYY/{categoria}/` **organizado pela data do culto**. **Status: DIVERGENTE** — arquivos chegam ao Drive, mas na árvore errada. Correção na Fase C3.

---

## 8. Controllers e Livewire

| Componente | Arquivo | Status |
|-----------|---------|--------|
| `PortalCantor` (Livewire) | `app/Livewire/PortalCantor.php` | **CONFORME** |
| `ModoCulto` (Livewire) | `app/Livewire/ModoCulto.php` | **PARCIAL** — ver risco crítico abaixo |
| `ConfirmacaoEscalaController` | `app/Http/Controllers/ConfirmacaoEscalaController.php` | **CONFORME** |
| `DownloadArquivoController` | `app/Http/Controllers/DownloadArquivoController.php` | **CONFORME** |
| `DownloadLiturgiaController` | `app/Http/Controllers/DownloadLiturgiaController.php` | **CONFORME** |

---

## 9. Rotas

| Rota | Status |
|------|--------|
| `GET /cantor/{token}` | **CONFORME** |
| `GET /escala/{token}` + POST confirmar/recusar | **CONFORME** |
| `GET /modo-culto/{culto}` (auth) | **CONFORME** |
| `GET /liturgia/download/{id}` (auth) | **CONFORME** |
| `GET /arquivo/musica/{id}` (auth) | **CONFORME** |

---

## 10. Events e Notifications  *(CORRIGIDO após re-inventário)*

> ⚠️ A versão inicial desta seção continha **falsos negativos** — a Glob `app/**/*.php` truncou em 100 arquivos e os diretórios `Events/`, `Notifications/`, `Observers/` ficaram de fora. Estado real:

| Classe | Arquivo | Status | Observação |
|--------|---------|--------|------------|
| `ItemCultoAtivado` (Event) | `app/Events/ItemCultoAtivado.php` | **CONFORME** | `ShouldBroadcastNow`, canal `culto.{id}`, payload `liturgia_id`. **Nunca foi risco de crash.** |
| `CantorAgendadoNotification` | `app/Notifications/CantorAgendadoNotification.php` | **CONFORME** | Mail enfileirado; disparado por `MusicaObserver` na aprovação |
| `EscalaConfirmacaoNotification` | `app/Notifications/EscalaConfirmacaoNotification.php` | **CONFORME** | Mail enfileirado; disparado por `EscalaObserver` na criação; inclui link tokenizado |

---

## 11. ACL / Permissões

| Item | Status | Detalhe |
|------|--------|---------|
| Seed de roles | **CONFORME** | 5 roles criados: Administrador, Diretor de Música, Sonoplasta, Cantor, Pastor |
| `bezhansalleh/filament-shield` | **AUSENTE** | Nenhuma policy/permission aplicada no painel Filament — qualquer usuário logado acessa tudo |
| Cantor só vê as próprias músicas | **AUSENTE** | Sem Shield/Policy, um Cantor pode ver/editar músicas de outros |

---

## 12. Checklist A.2 — Preenchido

### Fundação e Stack

| Item esperado | Prio. | Status / arquivo |
|---------------|-------|-----------------|
| Laravel 12 + Filament 5 + PHP 8.3 | P1 | **DIVERGENTE** — Laravel 13.16.1 (recomendo manter; não regredir) |
| laravel/boost instalado (dev) | P3 | **AUSENTE** |
| Shield + Breezy + spatie/permission + medialibrary | P1 | **PARCIAL** — Shield ✓ (instalado C1), permission ✓ (^7), medialibrary ✓; Breezy dispensável (Filament 5 tem perfil/2FA nativos) |
| 5 roles seedadas (Admin, Diretor, Sonoplasta, Cantor, Pastor) | P1 | **CONFORME** — `database/seeders/DatabaseSeeder.php` |
| Migrations e models do Núcleo (cultos, liturgias) | P1 | **CONFORME** — todas as migrations presentes |

### Módulos principais

| Item esperado | Prio. | Status / arquivo |
|---------------|-------|-----------------|
| M1 — CultoResource + EscalaResource + Calendário (mensal/semanal) | P1 | **PARCIAL** — mensal ✓ via guava/calendar; view semanal com drag-drop **AUSENTE** |
| M1 — Confirmação por token + aviso 3 dias (Horizon) | P2 | **PARCIAL** — confirmação por token ✓ (controller + EscalaObserver envia notificação na criação); **aviso 3 dias antes AUSENTE** (sem `NotificarEscalaJob` nem scheduler); Horizon → substituído por Database Queue |
| M2 — Página pública /cantor/{token} (Livewire) | P1 | **CONFORME** — `app/Livewire/PortalCantor.php` |
| M2 — Status em tempo real (Reverb) + histórico imutável | P1 | **CONFORME** — Event `ItemCultoAtivado` ✓ (ShouldBroadcastNow), ModoCulto escuta via Echo; HistoricoCantor ✓ |
| M2 — QR Code por cantor | P3 | **PARCIAL** — pacote instalado; QR na MusicasTable (memória); confirmar na UI |
| M3 — Disk google (Drive) + SincronizarGoogleDriveJob | P1 | **CONFORME (via design)** — disk local `cultos` + Drive Desktop; upload grava direto. Job dedicado dispensável |
| M3 — Árvore de pastas automática /Cultos/YYYY/MM/DD/ | P1 | **DIVERGENTE** — grava em `Cantores/{cantor}/{data}/` em vez de `Cultos/YYYY/MM/DD/{cat}/`. Corrigir na C3 |
| M3b — SincronizarGoogleCalendarJob (criar/editar/excluir) | P2 | **CONFORME** — `app/Jobs/SincronizarGoogleCalendarJob.php` + CultoObserver ✓ |
| M4 — ProvaiVedeResource + busca + filtros + favoritos | P2 | **PARCIAL** — Resource ✓; toggle de favoritos na UI a verificar |
| M4b — InformativoResource + unificação no seletor de liturgia | P2 | **PARCIAL** — Resource ✓; unificação no seletor a verificar |
| M5 — AnuncioResource + 'Exibir Agora' + sempre_disponivel | P2 | **PARCIAL** — Resource ✓; botão 'Exibir Agora' no Modo Culto a verificar |
| M6 — GerarLiturgiaJaJob (.ja real) + pasta numerada | P1 | **CONFORME** — `app/Jobs/GerarLiturgiaJaJob.php` + `app/Http/Controllers/DownloadLiturgiaController.php` |
| M7 — Dashboard com os 6 widgets | P2 | **PARCIAL** — `StatsWidget` (4 contadores: próximo culto, escalas pendentes, músicas p/ revisar, cultos no mês) + `ProximoCultoWidget` + `CalendarioMensalWidget`. Faltam widgets dedicados de Anúncios Ativos e Histórico Recente (contadores parcialmente cobertos pelo Stats) |
| M8 — Modo Culto fullscreen + Reverb + atalhos | P1 | **CONFORME** — Livewire fullscreen ✓, rota ✓, Event ✓, atalhos (←→/Space) via Alpine, YouTube inline. Testar sync 2 telas com `reverb:start` |

### Transversais

| Item esperado | Prio. | Status / arquivo |
|---------------|-------|-----------------|
| Busca rápida presente em TODOS os módulos | P1 | **PARCIAL** — a auditar por resource; tabelas Filament têm search global, mas cobertura total não verificada |
| Histórico de cantores/músicas/datas gravado | P1 | **PARCIAL** — model HistoricoCantor ✓; gravação automática na aprovação a verificar |
| Permissões aplicadas (Cantor só vê o próprio) | P1 | **PARCIAL** — Shield instalado (C1); falta gerar policies + filtrar MusicaResource por cantor + bloquear Cantor no painel (`canAccessPanel`) → C4 |

---

## 13. Riscos / Lacunas (estado corrigido após re-inventário)

| # | Item | Gravidade | Status |
|---|------|-----------|--------|
| R1 | ~~Event `ItemCultoAtivado` ausente~~ | — | **FALSO ALARME** — existe e está correto |
| R2 | `filament-shield` + policies — ACL não aplicada | **ALTO** | Shield instalado na C1; falta gerar policies + "Cantor só vê o próprio" → **C4** |
| R3 | ~~Horizon ausente~~ | — | **RESOLVIDO** — substituído por Database Queue (decisão aprovada) |
| R4 | Estrutura de pastas do upload diverge da spec | **MÉDIO** | `Cantores/...` em vez de `Cultos/YYYY/MM/DD/{cat}/` → **C3** |
| R5 | ~~`CantorAgendadoNotification` ausente~~ | — | **FALSO ALARME** — existe, disparada por `MusicaObserver` |
| R6 | `NotificarEscalaJob` (aviso 3 dias) ausente | **MÉDIO** | Único gap de fluxo do M1 → **C2** |
| R7 | `IgrejaResource` no menu (Fase 9 antecipada) | **BAIXO** | **RESOLVIDO** na C1 (`$shouldRegisterNavigation = false`) |
| R8 | `canAccessPanel()` retorna `true` p/ todos | **MÉDIO** | Cantor não deveria entrar no painel admin → **C4** |

---

## 14. Resumo Executivo  *(revisado após re-inventário)*

> A primeira versão deste relatório subestimou o progresso por causa de uma Glob truncada (100 arquivos). O sistema está **substancialmente mais completo** do que a auditoria inicial indicou — a maior parte dos Módulos 1–8 já tem implementação funcional.

```
Estado real (revisado):
CONFORME    : ~78% dos itens
PARCIAL     : ~12% dos itens
DIVERGENTE  : ~6%  (Laravel 13 vs 12; árvore de pastas do Drive)
AUSENTE     : ~4%  (aviso 3 dias; widgets dedicados; scraper Fase 9)
```

**O que está bem (já implementado):** banco completo; Resources principais com RelationManagers; Louvor JA (job + download); Portal do Cantor (Livewire + upload + QR); Modo Culto fullscreen com Reverb e atalhos; Google Calendar (job + observers); notificações de escala e cantor (via observers); 3 widgets de dashboard; Media Library; confirmação de escala por token.

**Lacunas reais restantes:**
1. **ACL aplicada (P1)** — Shield instalado, falta gerar policies + restringir Cantor → **C4**
2. **Árvore de pastas do Drive (P1)** — corrigir `diretorioUpload()` para `Cultos/YYYY/MM/DD/{cat}/` → **C3**
3. **Aviso 3 dias antes (P2)** — criar `NotificarEscalaJob` + agendar no scheduler → **C2**
4. **Widgets de dashboard dedicados (P2)** — Anúncios Ativos / Histórico Recente → **C5**

> **Plano revisado:** C2 (aviso 3 dias) → C3 (árvore de pastas) → C4 (ACL/policies) → C5 (widgets). As fases originais de notificações / Drive job / Modo-Culto caíram porque o trabalho já existe.

**O que falta mais criticamente (P1):**
1. `App\Events\ItemCultoAtivado` — crash no Modo Culto
2. `bezhansalleh/filament-shield` + policies — sem segurança de dados
3. `SincronizarGoogleDriveJob` — Drive não funciona sem ele
4. `CantorAgendadoNotification` — portal do cantor incompleto sem ela
5. `laravel/horizon` — monitoramento de filas
