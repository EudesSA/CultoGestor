# Deploy do CultoGestor — HostGator (compartilhado / cPanel)

Guia passo a passo para colocar o CultoGestor no ar em hospedagem **compartilhada** da HostGator. Tempo estimado: ~1h na primeira vez.

> **Decisões desta hospedagem:** Modo Culto em tempo real via **Pusher** (compartilhado não roda Reverb); arquivos salvos **no próprio servidor**; tarefas agendadas e fila via **cron** (sem daemon).

---

## 0. Pré-requisitos

- [ ] Plano HostGator com **PHP 8.3** (cPanel → *MultiPHP Manager*) e **MySQL**.
- [ ] **SSL** ativo no domínio (cPanel → *SSL/TLS Status* → *Run AutoSSL*). Obrigatório para PWA, push e o seletor de monitor.
- [ ] **Acesso SSH** ou **cPanel → Terminal** (necessário para rodar `php artisan`). Em alguns planos o SSH precisa ser habilitado no suporte.
- [ ] Conta gratuita no **Pusher**: https://pusher.com → crie um app *Channels* → anote App ID, Key, Secret e Cluster.
- [ ] Node/NPM **na sua máquina local** (para gerar os assets; o servidor não precisa de Node).

---

## 1. Banco de dados (cPanel)

1. cPanel → **MySQL Databases**.
2. Crie um banco (ex.: `cpaneluser_cultogestor`).
3. Crie um usuário + senha forte e **adicione-o ao banco com ALL PRIVILEGES**.
4. Anote `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (já vêm com o prefixo do cPanel).

---

## 2. Preparar os arquivos localmente

Na sua máquina, dentro do projeto:

```bash
# dependências de produção (sem dev)
composer install --no-dev --optimize-autoloader

# assets do front-end (gera public/build com as credenciais do Pusher)
# IMPORTANTE: defina antes os VITE_* de produção (ou rode com o .env.production)
npm install
npm run build
```

> Os `VITE_*` são "queimados" no build. Garanta que `VITE_BROADCAST_DRIVER=pusher`, `VITE_PUSHER_APP_KEY` e `VITE_PUSHER_APP_CLUSTER` estejam corretos **antes** do `npm run build`.

Compacte o projeto (sem `node_modules`, com `vendor` e `public/build`):

```bash
# exemplo (git bash / linux): zip de tudo exceto node_modules e .git
zip -r cultogestor.zip . -x "node_modules/*" ".git/*" "*.env"
```

---

## 3. Enviar para o servidor

Estrutura recomendada: a aplicação fica **fora** do `public_html`.

1. cPanel → **File Manager** → vá para a home (`/home/cpaneluser`).
2. Crie a pasta `cultogestor` e envie/extraia o `cultogestor.zip` dentro dela.
3. Resultado: `/home/cpaneluser/cultogestor/` com `app/`, `public/`, `vendor/`, etc.

### Apontar o domínio para `public/`

**Opção A (preferida):** cPanel → **Domains** → edite o domínio → *Document Root* = `/home/cpaneluser/cultogestor/public`.

**Opção B (se não puder mudar o Document Root):** deixe o app em `~/cultogestor`, copie o **conteúdo** de `cultogestor/public/` para `public_html/` e edite `public_html/index.php`:

```php
require __DIR__.'/../cultogestor/vendor/autoload.php';
$app = require_once __DIR__.'/../cultogestor/bootstrap/app.php';
```

Garanta que o `public_html/.htaccess` (que veio de `public/`) esteja presente.

---

## 4. Configurar o `.env`

1. No servidor, copie `.env.production` para `.env` (na raiz de `~/cultogestor`).
2. Preencha: `APP_URL=https://seu-dominio`, dados do **banco**, credenciais do **Pusher**, senha do **e-mail**.
3. Gere a chave da aplicação (via Terminal/SSH):

```bash
cd ~/cultogestor
php artisan key:generate
```

---

## 5. Banco, permissões e otimização (via SSH/Terminal)

```bash
cd ~/cultogestor

# 1) Migrations + permissões (Shield) + seed inicial (roles, tipos, funções)
php artisan migrate --force
php artisan shield:generate --all --option=policies_and_permissions --panel=admin --no-interaction
php artisan db:seed --force

# 2) Link de storage (mídias públicas) — se 'ln' não funcionar, veja a nota abaixo
php artisan storage:link

# 3) Caches de produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 4) Permissões de escrita
chmod -R 775 storage bootstrap/cache
```

> **storage:link no compartilhado:** se o symlink falhar, crie manualmente em *File Manager* um link de `public/storage` → `../storage/app/public`, ou copie a pasta. As fotos/thumbs usam o disco `public`.

> **Sempre que mudar o `.env` em produção, rode `php artisan config:cache` de novo.**

---

## 6. Tarefas agendadas e fila (cron)

cPanel → **Cron Jobs**. Adicione os dois (ajuste o caminho do PHP do cPanel, ex.: `/usr/local/bin/php` ou `/opt/cpanel/ea-php83/root/usr/bin/php`):

**Agendador (aviso 3 dias antes, scraper semanal):** a cada minuto
```
* * * * * cd /home/cpaneluser/cultogestor && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
```

**Fila (e-mails e push):** a cada minuto
```
* * * * * cd /home/cpaneluser/cultogestor && /usr/local/bin/php artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
```

> Sem esses crons, e-mails/push/lembretes não são enviados.

---

## 7. Primeiro acesso

- Acesse `https://seu-dominio/admin`.
- Login inicial (do seeder): **admin@cultogestor.com / password** → **troque a senha imediatamente** (perfil do usuário).
- Cadastre: a **Igreja** (Configurações), as **funções de cada membro** (Usuários → Funções no culto), as **playlists** do Provai e Vede.

---

## 8. Verificação pós-deploy (checklist)

- [ ] Página abre em **HTTPS** sem erro 500 (se der 500, veja `storage/logs/laravel.log`).
- [ ] Login no `/admin` funciona.
- [ ] **E-mail**: criar uma escala → o membro recebe o e-mail (cron de fila rodando).
- [ ] **Modo Culto**: abrir `/modo-culto/{id}` em 2 abas → avançar item sincroniza (Pusher).
- [ ] **PWA**: abrir o portal do cantor no celular → "Instalar app" e "Ativar notificações".
- [ ] **Push**: aprovar uma música → chega notificação no celular do cantor.
- [ ] **Upload**: enviar arquivo no portal → aparece e baixa.

---

## 9. Atualizações futuras (re-deploy)

```bash
cd ~/cultogestor
# suba os arquivos alterados, então:
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache
# se mudou assets: rode 'npm run build' local e suba o public/build
```

---

## Notas importantes do ambiente compartilhado

- **Reverb não roda** aqui → por isso usamos **Pusher**. Se um dia migrar para VPS, dá para voltar ao Reverb (mude `BROADCAST_CONNECTION` e `VITE_BROADCAST_DRIVER`).
- **Google Drive Desktop não existe** no servidor → os arquivos ficam em `storage/app/cultos`. Fique de olho no **limite de espaço/inodes** do plano. Para centralizar no Drive, seria preciso integrar a **API do Google Drive** (decisão adiada).
- **Pail / Horizon** não se aplicam (precisam de extensões/daemon indisponíveis).
