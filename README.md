# CultoGestor

> Central operacional web para a **gestão técnica do culto** — sonoplastia, projeção, transmissão, cantores especiais, anúncios e comunicação.

O CultoGestor substitui planilhas, grupos de WhatsApp e pastas manuais por um sistema único: agenda de cultos, escalas de equipe, portal do cantor, bibliotecas de vídeo/anúncios, exportação para o **Louvor JA** e um **Modo Culto** em tempo real para os operadores.

---

## ✨ Principais módulos

- **Calendário & Cultos** — agenda visual (mês/semana/lista), tipos de culto, arrastar-e-soltar para reagendar, integração com Google Calendar.
- **Escalas** — montagem de equipe por função (Sonoplasta, Projeção, Transmissão, Músico, etc.) com confirmação por link tokenizado.
- **Portal do Cantor** — link próprio para o cantor enviar suas músicas (YouTube, playback, letra, cifra), com histórico imutável de participações.
- **Bibliotecas** — Provai e Vede, Informativos e Anúncios com mídias e player inline.
- **Louvor JA** — geração da liturgia `.ja` para importação direta no software Louvor JA.
- **Modo Culto** — tela cheia em tempo real (Reverb) sincronizando o item ativo entre todos os operadores.
- **Dashboard & Relatórios** — widgets de programação, escalas e participação; exportação em PDF/XLS.

---

## 🧱 Stack

| Camada | Tecnologia |
|--------|------------|
| Linguagem | PHP 8.3 |
| Framework | Laravel |
| Painel admin | Filament 5 |
| Permissões | Filament Shield + spatie/laravel-permission |
| Banco | MySQL 8 |
| Tempo real | Laravel Reverb (WebSockets) |
| Filas | Laravel Horizon / queue worker |
| Mídia | spatie/laravel-medialibrary |
| Integrações | Google Drive, Google Calendar, YouTube |

Ambiente de desenvolvimento: **Laragon** (Windows).

---

## 🔐 Papéis e permissões

| Papel | Resumo de acesso |
|-------|------------------|
| **Administrador** | Acesso total (super admin). |
| **Diretor de Música** | CRUD operacional completo; cria cultos; aprova/reprova músicas. |
| **Sonoplasta** | Leitura geral + gestão de cultos (criar/editar/reagendar). |
| **Pastor** | Visualização geral (somente leitura). |
| **Cantor** | Gerencia apenas as próprias músicas (criar, editar, excluir); não altera o status. |

> O recorte "o cantor só enxerga as próprias músicas" é aplicado por *query* no `MusicaResource`, além das permissões do Shield.

---

## 🚀 Instalação local

Pré-requisitos: PHP 8.3, Composer, Node.js + npm, MySQL 8.

```bash
# 1. Clonar
git clone https://github.com/EudesSA/CultoGestor.git
cd CultoGestor

# 2. Dependências
composer install
npm install

# 3. Ambiente
cp .env.example .env
php artisan key:generate
# configure DB_*, MAIL_*, GOOGLE_* e LOUVORJA_BASE_PATH no .env

# 4. Banco + permissões
php artisan migrate
php artisan db:seed

# 5. Assets + servidor
npm run build
php artisan serve
```

Para subir tudo de uma vez em desenvolvimento (servidor + fila + Reverb + Vite):

```bash
composer run dev
```

---

## ⚙️ Configurações importantes (`.env`)

- `LOUVORJA_BASE_PATH` — caminho-raiz local do Louvor JA usado na geração do `.ja`. **Nunca** hardcode caminhos de máquina; configure aqui.
- Credenciais do **Google** (service account) para Drive e Calendar.
- Configuração do **Reverb** para o Modo Culto em tempo real.

---

## 📂 Estrutura (resumo)

```
app/
├── Filament/Resources/   Recursos do painel (Culto, Escala, Musica, Cantor, ...)
├── Filament/Widgets/     Calendário, próximo culto, estatísticas, ...
├── Http/Controllers/     Downloads (.ja, arquivos), confirmações por token
├── Livewire/             ModoCulto, PortalCantor
├── Jobs/                 Liturgia .ja, Google Calendar, notificações
├── Models/               Culto, Musica, Cantor, Escala, ... (PT-BR)
└── Policies/             Autorização por recurso (via Shield)
```

---

## 📄 Licença

Projeto proprietário. Todos os direitos reservados.

---

## 👤 Autoria

Desenvolvido por **Eudes** — **ProezaTech**.
