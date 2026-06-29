# CLAUDE.md — CultoGestor

> Arquivo de memória do projeto para o Claude Code. **Leia este arquivo inteiro antes de qualquer plano ou alteração.** Ele tem precedência sobre suposições genéricas de Laravel/Filament.

---

## 0. Como você (agente) deve operar neste repositório

Estas regras existem porque, sem elas, o planejamento sai do trilho.

1. **Sempre consulte o Laravel Boost MCP** antes de escrever qualquer código de Filament, Livewire ou Laravel. As APIs do Filament 5 mudaram em relão às v3/v4. **Não gere código de memória.** Se o Boost não tiver a resposta, diga isso explicitamente e proponha verificar a doc — não invente assinaturas de métodos, namespaces ou imports.
2. **Siga a ordem das fases** (seção 9). No modo planejamento, o plano que você produzir deve mapear-se diretamente a uma fase. Não pule fases nem antecipe módulos de "Melhorias Avançadas" (Fase 9) sem pedido explícito.
3. **Documento-mestre:** o plano funcional completo está em `docs/PLANO.md` (ou no anexo fornecido pelo desenvolvedor). Quando houver dúvida de escopo, esse documento manda — este CLAUDE.md cuida de *como* construir, o PLANO cuida de *o quê*.
4. **Antes de instalar qualquer pacote**, verifique se existe release estável compatível com **Filament 5**. Vários pacotes do ecossistema só têm versão para v3/v4 (ver seção 8). Em caso de incompatibilidade, pare e proponha alternativa — não force `--ignore-platform-reqs` nem `:dev-main` sem aprovação.
5. **Não rode migrations destrutivas** (`migrate:fresh`, `migrate:rollback`) sem confirmar. O banco pode ter dados de teste que importam.
6. **Idioma do domínio é português.** Models, colunas, rotas e labels em PT-BR (ver seção 6). Código (variáveis internas, métodos) pode ser em inglês quando for convenção do framework.
7. **Ao planejar, entregue:** (a) a fase alvo, (b) arquivos que serão criados/alterados, (c) migrations envolvidas, (d) pacotes a instalar com versão, (e) riscos/pendências de verificação. Sem isso o plano está incompleto.

---

## 1. Visão Geral

CultoGestor é uma central operacional web para a **operação técnica do culto** (sonoplastia, projeção, transmissão, cantores especiais, anúncios e comunicação), substituindo planilhas, grupos de WhatsApp e pastas manuais. Integra-se ao Google Drive e ao Google Calendar e exporta liturgias para o software **Louvor JA**.

Projeto novo, partindo do zero em `C:\laragon\www\CultoGestor`.

---

## 2. Stack (fixa)

```
PHP 8.3
Laravel 12
Filament 5
MySQL 8 (Laragon)
Reverb (WebSockets)   — sync em tempo real do Modo Culto
Horizon (filas)       — uploads Drive, notificações, jobs agendados
```

Ambiente local: **Laragon** (Windows). Há projetos Filament 3 de referência no ambiente (HectareDrone, admin-filament) — **não use-os como modelo de API**, pois são de uma major diferente.

---

## 3. Convenções Filament 5 — ARMADILHAS CONHECIDAS

> Estas vêm de experiência real em projetos Filament v5 da mesma stack. Respeite-as; elas evitam bugs silenciosos.

- **Imports diferentes do esperado:** namespaces de `Section`, `Get`, `Set` e afins **não** seguem os caminhos antigos da v3. Confirme o namespace correto via Boost antes de usar — não copie de tutoriais de Filament 3/4.
- **`Model::creating()` e tenancy:** um listener automático do Filament pode sobrescrever a coluna de tenant (ex.: `igreja_id`) ao criar registros. Para criação cross-tenant ou em seeds/jobs, use `Model::withoutEvents(fn () => ...)`.
- **`canCreate()` / `can*()` devem ser sem efeitos colaterais.** Nunca dispare queries de escrita, jobs ou mutações dentro desses métodos — eles são chamados múltiplas vezes no render.
- **Livewire não injeta dependências em métodos de `wire:click`.** Não declare DI na assinatura do método chamado pelo `wire:click`; resolva inline com `app(MeuServico::class)`.
- **Livewire não retorna downloads de arquivo diretamente.** Para baixar `.ja`, PDF, XLS, mídia: crie uma **rota de controller** e aponte um `<a href="..." target="_blank">`. Não tente `return response()->download()` de dentro de um componente Livewire/ação Filament.
- **Verifique sempre a v5 antes:** schemas de formulário, infolists, actions e a estrutura de Resources mudaram entre majors. Boost primeiro.

---

## 4. Uso do Laravel Boost (obrigatório)

`laravel/boost` está instalado como dev e expõe documentação versionada via MCP.

- Antes de gerar Resource, Widget, Form schema, Table, Action, Notification ou rota → **consulte o Boost** para a sintaxe da versão instalada.
- Use `php artisan boost:install` na Fase 1.
- Se uma resposta sua depende de API específica do Filament 5 e o Boost não confirma, **sinalize a incerteza** em vez de afirmar.

---

## 5. Estrutura de Pastas (alvo)

```
app/
├── Filament/
│   ├── Resources/      CultoResource, EscalaResource, MusicaResource,
│   │                   CantorResource, ProvaiVedeResource, InformativoResource,
│   │                   AnuncioResource, UserResource
│   ├── Pages/          Dashboard, ModoCulto
│   └── Widgets/        CultosSemanaisWidget, MusicasPendentesWidget,
│                       ProximoCultoWidget, EscalasPendentesWidget, ...
├── Http/
│   ├── Controllers/    PortalCantorController (tokenizado),
│   │                   ModoCultoController, DownloadLiturgiaController
│   └── Livewire/       PortalCantorForm, ModoCultoLive
├── Jobs/               SincronizarGoogleDriveJob, SincronizarGoogleCalendarJob,
│                       GerarLiturgiaJaJob, MonitorarProvaiVedeJob, NotificarEscalaJob
├── Models/             Culto, CultoTipo, CultoLiturgia, Escala, Funcao,
│                       Cantor, Musica, MusicaArquivo, HistoricoCantor,
│                       ProvaiVede, Informativo, Anuncio, Hino
└── Notifications/      CantorAgendadoNotification, EscalaConfirmacaoNotification
```

---

## 6. Convenções de Código e Nomenclatura

- **Tabelas e colunas em PT-BR, snake_case:** `cultos`, `culto_liturgias`, `hora_inicio`, `confirmado_em`.
- **Models em PT-BR singular:** `Culto`, `Musica`, `Cantor`.
- **Enums de status como string controlada:** `pendente`, `enviado`, `revisado`, `aprovado`. Prefira PHP enums tipados.
- **Rotas públicas tokenizadas:** `/cantor/{token}`, `/modo-culto/{culto}`.
- **Migrations:** uma responsabilidade por arquivo; sempre `down()` coerente.
- **`igreja_id` nullable já nas entidades centrais** (cultos, escalas, musicas, anuncios, provai_vede, informativo) desde a Fase 1, mesmo com multi-igreja adiado — evita migration dolorosa depois. Não construa a UI de multi-igreja agora; só reserve a coluna.

---

## 7. Esquema do Banco (referência condensada)

Detalhe completo no PLANO. Núcleo:

```
users(name, email, phone_whatsapp, role, avatar)
culto_tipos(nome, cor)
cultos(tipo_id, igreja_id?, data, hora_inicio, hora_fim, tema, status)
culto_liturgias(culto_id, ordem, tipo[musica/video/anuncio/item], titulo, duracao_min, observacao)
culto_liturgia_itens(liturgia_id, referencia_tipo, referencia_id)   -- polimórfico

funcoes(nome)            -- Sonoplasta, Projeção, Transmissão, Músico, Fotógrafo, Cantor Especial
escalas(culto_id, funcao_id, user_id, confirmado, confirmado_em)

cantores(user_id, voz, observacoes)
musicas(cantor_id, culto_id, nome, artista, tom, duracao_segundos, link_youtube, status, enviado_em, aprovado_em)
musica_arquivos(musica_id, tipo[mp3/playback/letra/cifra/partitura], path_drive, media_id)
historico_cantores(cantor_id, culto_id, musica_id, observacao)      -- IMUTÁVEL, log automático

provai_vede(titulo, tema, categoria, data_publicacao, duracao_segundos, link_youtube, thumbnail_path, ativo)
informativo(...)         -- mesma estrutura do provai_vede, categorias próprias
anuncios(titulo, tipo, categoria, data_inicio, data_fim, ativo, sempre_disponivel, ordem)
anuncio_midias(anuncio_id, tipo[video/imagem/slide], path_drive, media_id)

louvorja_exportacoes(culto_id, arquivo_liturgia_ja, gerado_em, path)
google_calendar_eventos(culto_id, google_event_id, calendar_id, sincronizado_em)
```

`historico_cantores` é **append-only**: nunca atualizar nem deletar registros; só inserir.

---

## 8. Pacotes — verificar compatibilidade com Filament 5 ANTES de instalar

| Pacote | Status / Atenção |
|--------|------------------|
| `filament/filament ^5` | Core. |
| `bezhansalleh/filament-shield` | **Verificar tag p/ v5.** Se não houver, usar autorização nativa do Filament 5 + `spatie/laravel-permission`. |
| `jeffgreco13/filament-breezy` | **Provavelmente dispensável** — Filament 5 já tem perfil/2FA nativos. Não instalar sem necessidade. |
| `spatie/laravel-permission ^6` | OK. |
| `spatie/laravel-medialibrary ^11` | OK. |
| `masbug/flysystem-google-drive-ext` | OK. Preferir **service account** a refresh token para uso server-side. |
| `spatie/laravel-google-calendar` | Requer **service account** com o calendário compartilhado. |
| `laravel/reverb`, `laravel/horizon` | OK. |
| `leandrocfe/filament-ptbr-form-fields` | **Verificar suporte v5.** |
| `barryvdh/laravel-dompdf`, `maatwebsite/excel`, `simplesoftwareio/simple-qrcode`, `laravel/socialite` | Independentes do Filament — OK. |

Regra: nenhum pacote entra com `:dev-main` ou `--ignore-platform-reqs` sem aprovação explícita.

---

## 9. Ordem de Implementação (fases — não pular)

- **Fase 1 — Fundação:** `create-project` → `boost:install` → Filament 5 → pacotes core → migrations + models (com `igreja_id?`) → seed de roles (Administrador, Diretor, Sonoplasta, Cantor, Pastor) → ACL.
- **Fase 2 — Calendário e Escalas:** `CultoResource`, `EscalaResource`, widget de calendário, confirmação por token.
- **Fase 3 — Portal do Cantor:** `musicas`, `musica_arquivos`, `historico_cantores`; `MusicaResource`; página pública `/cantor/{token}` em Livewire (sem Filament).
- **Fase 4 — Google Drive + Calendar:** disk `google`, `SincronizarGoogleDriveJob`, estrutura de pastas; `SincronizarGoogleCalendarJob`.
- **Fase 5 — Bibliotecas de Vídeo + Anúncios:** `ProvaiVedeResource`, `InformativoResource`, `AnuncioResource` + Media Library.
- **Fase 6 — Louvor JA Export:** `GerarLiturgiaJaJob` + download via controller (ver seção 10).
- **Fase 7 — Modo Culto:** Livewire fullscreen `/modo-culto/{culto}` + Reverb (sync multi-tela) + player inline + atalhos.
- **Fase 8 — Dashboard + Polimento:** widgets, notificações email, relatórios PDF/XLS.
- **Fase 9 — Avançado (só sob pedido):** scraper Provai e Vede, WhatsApp (Z-API/Twilio), PWA, avaliação pós-culto, Banco de Hinos, Ensaio, Multi-igreja.

---

## 10. Regras específicas por módulo

**Exportação Louvor JA (Módulo 6) — atenção máxima:**
- O `.ja` referencia **caminhos locais Windows do Louvor JA** (ex.: `C:\...\G\Cultos\...`), que diferem da estrutura no Drive.
- O caminho-raiz local **deve ser configurável** (ex.: `LOUVORJA_BASE_PATH` no `.env`). **Não hardcode** `C:\Users\LENOVO\...`.
- O download do arquivo `.ja` e da pasta numerada sai por **rota de controller** (`DownloadLiturgiaController`), nunca direto do Livewire.

**Portal do Cantor (Módulo 2):**
- Acesso por **link tokenizado**, sem login Filament completo. Página em Livewire puro.
- Cantor só enxerga as próprias músicas (validar no controller/policy, não só na UI).

**Modo Culto (Módulo 8):**
- Sync em tempo real via Reverb (broadcast do item ativo para todos os operadores).
- Sem layout Filament — Livewire fullscreen.

**Google Drive:** estrutura `/CultoGestor/Cultos/YYYY/MM/DD-MM-YYYY/{categoria}/` criada automaticamente no upload, registrando `path_drive`/`media_id`.

---

## 11. Comandos comuns

```bash
# Setup
composer create-project laravel/laravel CultoGestor
composer require laravel/boost --dev
php artisan boost:install
php artisan filament:install --panels

# Dev
php artisan migrate
php artisan db:seed
php artisan serve            # ou via Laragon
php artisan horizon
php artisan reverb:start
php artisan queue:work       # se não usar Horizon localmente

# Filament
php artisan make:filament-resource Culto --generate
php artisan make:filament-widget ProximoCultoWidget
```

---

## 12. Anti-padrões — NÃO FAÇA

- Não gerar código Filament a partir de exemplos de Filament 3/4 (HectareDrone, admin-filament) — major diferente.
- Não retornar downloads de dentro de Livewire/ações Filament.
- Não colocar efeitos colaterais em `canCreate()`/`can*()`.
- Não hardcodar caminhos de máquina no gerador `.ja`.
- Não instalar pacote sem confirmar compatibilidade com Filament 5.
- Não rodar `migrate:fresh`/`rollback` sem confirmação.
- Não pular fases nem antecipar Fase 9.
- Não escrever nem deletar em `historico_cantores`; apenas inserir.
- Não inventar API: na dúvida, consulte o Boost ou sinalize a incerteza.

---

## 13. Checklist de verificação (DoD por fase)

1. Escala: criar culto → escalar → confirmar por link → ver no calendário.
2. Cantor: agendar → notificação → upload no portal → aprovar → arquivo no Drive.
3. Drive: estrutura `/CultoGestor/Cultos/YYYY/MM/DD/` correta.
4. Louvor JA: gerar `.ja` → importar no Louvor JA → itens corretos.
5. Modo Culto: 2 dispositivos → avançar item em um → sync no outro (Reverb).
6. Permissões: como Cantor, só enxergar as próprias músicas.
7. Dashboard: contadores corretos (pendentes, ativos, escalas).
8. Calendar: criar/editar/cancelar culto reflete no Google Calendar.
9. Informativo: cadastrar → selecionar na liturgia → aparecer no slot certo do Modo Culto.
