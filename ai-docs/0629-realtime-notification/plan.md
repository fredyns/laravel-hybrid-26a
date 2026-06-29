# Realtime Notification System — Implementation Plan

> Status: Draft / not yet implemented
> Stack: Laravel 13 · Livewire 4 · Flux UI Pro 2 · Tailwind 4 · Reverb · PostgreSQL · Redis · Docker
> Scope: Database-persisted notifications + realtime broadcast over WebSockets (Reverb), surfaced as a **bottom popup** (Flux toast) and an unread **bell** counter.

---

## 1. Goals

1. Persist notifications per-user in the database (queryable history, unread counts).
2. Deliver them in realtime over WebSockets using **Laravel Reverb** (no polling).
3. Show an **instant popup at the bottom of the screen** when a notification arrives (reuse the existing Flux `toast.group`).
4. Keep the **bell icon** in both layout modes (`switchable-top` / `switchable-side`) live with an unread badge.
5. Ship a Reverb **Docker** service wired into the existing `docker-compose.yml`.

---

## 2. Current State (what exists today)

| Area | Status | Notes |
| --- | --- | --- |
| `livewire/livewire` v4, `livewire/flux[-pro]` v2 | ✅ installed | SFC pages use `⚡` prefix, routed via `Route::livewire('…','pages::…')` |
| `laravel/reverb` | ❌ not installed | Not in `composer.json` |
| `laravel-echo` / `pusher-js` | ❌ not installed | Not in `package.json` |
| `config/broadcasting.php`, `config/reverb.php` | ❌ absent | Created by `reverb:install` |
| `routes/channels.php` | ❌ absent | Channel auth callbacks needed |
| Channels registered in `bootstrap/app.php` | ❌ no | `withRouting()` has `web`, `commands`, `health` only |
| `reverb` service in `docker-compose.yml` | ⚠️ stub exists | Lines 106–126: builds PHP image, runs `reverb:start`, but only DB/Redis env passed — **no Reverb credentials yet** |
| PHP extensions `pcntl`, `sockets` | ✅ present | `deployment/docker/php/Dockerfile` already installs both (required by Reverb) |
| Queue worker container | ❌ none | `QUEUE_CONNECTION=database`; broadcasting is **queued** — a worker is required (see §4.2) |
| Flux toast at bottom | ✅ present | `resources/views/layouts/app/switchable.blade.php` has a persisted `flux:toast.group` (default position **bottom end**) |
| Static bell button | ✅ present | Hard-coded `flux:button icon="bell"` in `switchable-top.blade.php`, `switchable-side.blade.php`, legacy `sidebar.blade.php` / `header.blade.php` |
| `User` model | ✅ `Notifiable` + **`HasUuids`** | ⚠️ UUID PK → notifications table must use `uuidMorphs` (see §5) |
| Vite JS entry | `resources/js/app.js` | Currently **empty**; loaded on every page via `@vite([...,'resources/js/app.js'])` in `partials/head.blade.php` |

### Project-specific gotchas (read before coding)
- **UUID morphs** — `User` uses `HasUuids`, so the generated `notifications` migration must swap `morphs('notifiable')` → `uuidMorphs('notifiable')`, else the relation breaks.
- **Queue worker is mandatory** — `ShouldBroadcast` and broadcast notifications are dispatched onto the queue (`database` driver). Without a running worker the popup never fires. Either run a worker container (§4.2) or use `ShouldBroadcastNow`.
- **Echo lives in `app.js`** (this project has no `bootstrap.js`).
- **Register the channels route** — `bootstrap/app.php` does not currently load `routes/channels.php`; add it.
- **Reverb service already stubbed** — augment the existing `docker-compose.yml` block rather than adding a new one.

---

## 3. Target Architecture

```
                       (1) $user->notify(new ResourceNotification(...))
                                          │
         ┌────────────────────────────────┼────────────────────────────────┐
         ▼                                                                  ▼
  database channel                                                  broadcast channel
  → notifications table                                  → queued job (database queue)
  (history + unread badge)                                            │
                                                                      ▼
                                                        Reverb server (ws :8080)
                                                        private: App.Models.User.{uuid}
                                                                      │  event: .notification
                                                                      ▼
                                                Laravel Echo (browser, pusher protocol)
                                                                      │
                            ┌─────────────────────────────────────────┴───────────┐
                            ▼                                                       ▼
                window.Flux.toast(...)  ← bottom popup            Livewire.dispatch('notification-received')
                (reuses existing toast.group)                              │
                                                                           ▼
                                                          <livewire:notification-bell> refreshes
                                                          unread count from DB
```

**Flow summary**
1. App code calls `$user->notify(...)`.
2. Notification fans out to **two channels**: `database` (persist) + `broadcast` (realtime).
3. The broadcast job pushes the payload to Reverb on the user's **private** channel `App.Models.User.{id}`.
4. Echo (browser) receives it via the Pusher protocol.
5. A thin JS handler fires the **bottom Flux toast** and dispatches a Livewire browser event so the **bell** re-reads its unread count.

---

## 4. Docker Configuration (explicitly requested)

### 4.1 Reverb service — complete the existing stub

`docker-compose.yml` already has a `reverb` service. Replace its `environment` block so Reverb gets its credentials and broadcasting config (everything else in the stub is fine):

```yaml
  reverb:
    build:
      context: ./deployment/docker/php
      dockerfile: Dockerfile
    container_name: livewire26d_reverb
    working_dir: /var/www/html
    volumes:
      - '.:/var/www/html'
    ports:
      - '${REVERB_PORT:-8080}:8080'
    environment:
      - APP_KEY=${APP_KEY}
      - BROADCAST_CONNECTION=reverb
      - DB_HOST=${DB_HOST}
      - DB_PORT=${DB_PORT}
      - DB_DATABASE=${DB_DATABASE}
      - DB_USERNAME=${DB_USERNAME}
      - DB_PASSWORD=${DB_PASSWORD}
      - REDIS_HOST=${REDIS_HOST}
      - REDIS_PORT=${REDIS_PORT}
      - REVERB_APP_ID=${REVERB_APP_ID}
      - REVERB_APP_KEY=${REVERB_APP_KEY}
      - REVERB_APP_SECRET=${REVERB_APP_SECRET}
      - REVERB_HOST=0.0.0.0
      - REVERB_PORT=8080
      # Optional horizontal scaling via the existing redis service:
      # - REVERB_SCALING_ENABLED=true
    depends_on:
      - postgres
      - redis
    networks:
      - livewire26d_net
    command: ['php', 'artisan', 'reverb:start', '--host=0.0.0.0', '--port=8080', '--debug']
```

Notes:
- `APP_KEY` is required because Reverb boots the full framework (config decryption, channel auth).
- Drop `--debug` once verified.
- The PHP image already has `pcntl` + `sockets`, so no Dockerfile change is needed.

### 4.2 Queue worker service — **new, required**

Broadcasting + broadcast-notifications run on the queue. Add a dedicated worker so events actually reach Reverb:

```yaml
  queue:
    build:
      context: ./deployment/docker/php
      dockerfile: Dockerfile
    container_name: livewire26d_queue
    working_dir: /var/www/html
    volumes:
      - '.:/var/www/html'
    environment:
      - APP_KEY=${APP_KEY}
      - BROADCAST_CONNECTION=reverb
      - DB_HOST=${DB_HOST}
      - DB_PORT=${DB_PORT}
      - DB_DATABASE=${DB_DATABASE}
      - DB_USERNAME=${DB_USERNAME}
      - DB_PASSWORD=${DB_PASSWORD}
      - REDIS_HOST=${REDIS_HOST}
      - REDIS_PORT=${REDIS_PORT}
      - REVERB_APP_ID=${REVERB_APP_ID}
      - REVERB_APP_KEY=${REVERB_APP_KEY}
      - REVERB_APP_SECRET=${REVERB_APP_SECRET}
      - REVERB_HOST=reverb
      - REVERB_PORT=8080
    depends_on:
      - postgres
      - redis
      - reverb
    networks:
      - livewire26d_net
    command: ['php', 'artisan', 'queue:work', '--tries=3', '--timeout=90']
```

> Server-side, the **app/queue containers reach Reverb at host `reverb`** (compose service name) on port `8080`. The **browser** reaches it at `localhost:${REVERB_PORT}` (see §7). Keep these distinct.

**Lightweight alternative (skip the worker):** make the realtime path synchronous by having the notification implement `ShouldBroadcastNow` (or set `BROADCAST_CONNECTION` to a sync-broadcasting path). Simpler for local dev, but blocks the web request during broadcast — fine for a starter kit, not ideal for production.

### 4.3 Pass Reverb env to the `app` (web/PHP-FPM) service

The web request signs channel-auth responses and may dispatch events, so add the same `REVERB_*` + `BROADCAST_CONNECTION=reverb` env to the existing `app` service (server-side host = `reverb`).

### 4.4 (Optional) Same-origin WebSocket proxy via nginx

For local dev, connecting the browser directly to `localhost:8080` is simplest (port already published). For a production-like single-origin setup, proxy `/app` (Reverb's Pusher endpoint) through the existing nginx `web` service — add to `deployment/docker/nginx/conf.d/app.conf`:

```nginx
location /app {
    proxy_pass http://reverb:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "Upgrade";
    proxy_set_header Host $host;
    proxy_read_timeout 600s;
}
```
Then the browser uses `wsHost=localhost`, `wsPort=<APP_PORT 9700>`. Decision deferred — default to the direct-port approach for now.

### 4.5 `.gitignore`

Reverb adds no new persistent volume. The existing `storage/docker/*` (postgres/redis/minio/mailpit) ignore rules already cover container state; nothing new required.

---

## 5. Phase A — Install Reverb & Broadcasting (server)

```bash
# inside the app container
docker compose exec app composer require laravel/reverb
docker compose exec app php artisan install:broadcasting --reverb --no-interaction
```

`install:broadcasting` will:
- publish `config/broadcasting.php` and `config/reverb.php`,
- create `routes/channels.php`,
- append `BROADCAST_CONNECTION`, `REVERB_*`, and `VITE_REVERB_*` to `.env`/`.env.example`,
- attempt to install `laravel-echo` + `pusher-js` and scaffold Echo (we override the JS in Phase D to match `app.js`).

**Register the channels route** — `install:broadcasting` adds `channels:` to `withRouting()` in `bootstrap/app.php` automatically. Verify it landed; if not, add manually:

```php
// bootstrap/app.php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    channels: __DIR__.'/../routes/channels.php', // ← add
    health: '/up',
)
```

**Channel authorization** — Laravel's broadcast-notification channel `App.Models.User.{id}` is private and authorized by the framework's built-in callback, but make it explicit in `routes/channels.php`:

```php
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function (User $user, string $id) {
    return $user->id === $id; // UUID string compare
});
```

---

## 6. Phase B — Notifications table (UUID-aware)

```bash
docker compose exec app php artisan make:notifications-table
```

⚠️ **Edit the generated migration before migrating** — because `User` uses `HasUuids`:

```php
// database/migrations/xxxx_create_notifications_table.php
Schema::create('notifications', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('type');
    $table->uuidMorphs('notifiable'); // ← was morphs('notifiable')
    $table->text('data');
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
});
```

```bash
docker compose exec app php artisan migrate
```

---

## 7. Phase C — Notification class

```bash
docker compose exec app php artisan make:notification ResourceNotification
```

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ResourceNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public ?string $url = null,
        public string $variant = 'success', // success | warning | danger
    ) {}

    /** @return array<int,string> */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /** @return array<string,mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
            'variant' => $this->variant,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
```

Fire it from anywhere (controller, Livewire action, job):

```php
$user->notify(new ResourceNotification(
    title: 'User created',
    body: "{$user->name} was added.",
    url: route('users.show', $user),
    variant: 'success',
));
```

Broadcast notifications are delivered to Echo as the event **`.notification`** on the private channel `App.Models.User.{id}`, with `type` (the notification class) plus the `toArray()` payload.

---

## 8. Phase D — Client: Echo + bottom popup wiring

### 8.1 Install client libs

```bash
docker compose exec app npm install --save-dev laravel-echo pusher-js
```

### 8.2 Configure Echo in `resources/js/app.js`

```js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

### 8.3 Subscribe + fire the bottom toast

The persisted `flux:toast.group` already renders at **bottom end**. Echo's `.notification()` handler calls the global `window.Flux.toast(...)` and pings Livewire so the bell refreshes. Add after the Echo init (guard on an authenticated user id exposed via a `<meta>` tag — see §8.4):

```js
const userId = document.querySelector('meta[name="user-id"]')?.content;

if (userId && window.Echo) {
    window.Echo.private(`App.Models.User.${userId}`)
        .notification((payload) => {
            window.Flux?.toast({
                heading: payload.title,
                text: payload.body,
                variant: payload.variant ?? 'success',
                duration: 6000,
            });

            window.Livewire?.dispatch('notification-received');
        });
}
```

> Want the popup **bottom-center** instead of bottom-right? Change the existing group in `switchable.blade.php`:
> `<flux:toast.group position="bottom center">`.

### 8.4 Expose the authenticated user id

Add to `resources/views/partials/head.blade.php` (inside `<head>`, after the meta tags):

```blade
@auth
    <meta name="user-id" content="{{ auth()->id() }}">
@endauth
```

---

## 9. Phase E — Live bell component (both layout modes)

Create a Livewire 4 SFC component that owns the unread badge and the dropdown list. Generate with:

```bash
docker compose exec app php artisan make:livewire NotificationBell
```

Component logic (`resources/views/livewire/⚡notification-bell.blade.php`, SFC style matching the project's `pages/**/⚡*.blade.php`):

```blade
<?php

use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    #[Computed]
    public function unreadCount(): int
    {
        return auth()->user()->unreadNotifications()->count();
    }

    #[Computed]
    public function recent(): Collection
    {
        return auth()->user()->notifications()->latest()->limit(10)->get();
    }

    // Re-render when JS receives a realtime push (dispatched from app.js).
    #[On('notification-received')]
    public function refresh(): void
    {
        unset($this->unreadCount, $this->recent);
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        unset($this->unreadCount, $this->recent);
    }
}; ?>

<flux:dropdown position="bottom" align="end">
    <flux:button variant="subtle" size="sm" square aria-label="{{ __('Notifications') }}">
        <flux:icon.bell variant="mini" />
        @if ($this->unreadCount > 0)
            <flux:badge size="sm" color="red" class="absolute -top-1 -end-1">{{ $this->unreadCount }}</flux:badge>
        @endif
    </flux:button>

    <flux:menu class="w-80">
        <div class="flex items-center justify-between px-2 py-1.5">
            <flux:heading size="sm">{{ __('Notifications') }}</flux:heading>
            @if ($this->unreadCount > 0)
                <flux:button variant="ghost" size="xs" wire:click="markAllRead">{{ __('Mark all read') }}</flux:button>
            @endif
        </div>
        <flux:menu.separator />

        @forelse ($this->recent as $n)
            <flux:menu.item :href="$n->data['url'] ?? '#'" wire:navigate class="{{ $n->read_at ? '' : 'font-semibold' }}">
                <div class="flex flex-col">
                    <span>{{ $n->data['title'] ?? __('Notification') }}</span>
                    <flux:text size="xs" class="opacity-70">{{ $n->data['body'] ?? '' }}</flux:text>
                </div>
            </flux:menu.item>
        @empty
            <div class="px-3 py-6 text-center">
                <flux:text size="sm" class="opacity-70">{{ __('No notifications yet') }}</flux:text>
            </div>
        @endforelse
    </flux:menu>
</flux:dropdown>
```

**Mount it in place of the static bell** in both partials:
- `resources/views/layouts/app/switchable-top.blade.php` (desktop right cluster **and** mobile context menu)
- `resources/views/layouts/app/switchable-side.blade.php` (top bar)

Replace each `<flux:button … icon="bell" … />` with:

```blade
<livewire:notification-bell />
```

> The legacy `sidebar.blade.php` / `header.blade.php` are not part of the active switchable layout — update only if still referenced.

---

## 10. Phase F — Tests (Pest)

Add feature tests (run in-container; the suite forces sqlite `:memory:` per `tests/TestCase.php`). `uuidMorphs` works on sqlite, so the notifications table migrates cleanly in tests.

```bash
docker compose exec app php artisan make:test NotificationBroadcastTest --pest
```

Cover:
1. **Persistence** — `$user->notify(...)` writes one row to `notifications` with the expected `data`.
2. **Broadcast dispatch** — `Notification::fake()` then assert `ResourceNotification` was sent on the `broadcast` + `database` channels to the user.
3. **Channel authorization** — a user authorizes their own `App.Models.User.{id}` channel and is rejected for another id.
4. **Bell component** — `Livewire::test(NotificationBell::class)` shows correct unread count; `markAllRead` zeroes it; the `notification-received` listener refreshes.

```bash
docker compose exec app php artisan test --compact --filter=Notification
```

---

## 11. Phase G — Run & verify locally

```bash
docker compose up -d reverb queue          # start ws server + worker
docker compose exec app npm run build      # compile Echo into the bundle
docker compose restart app                 # pick up new BROADCAST_CONNECTION env
```

Manual check:
1. Log in (browser tab open on `http://localhost:9700`).
2. Trigger a notification, e.g. from tinker:
   ```bash
   docker compose exec app php artisan tinker --execute 'App\Models\User::first()->notify(new App\Notifications\ResourceNotification("Hello","Realtime works",null,"success"));'
   ```
3. Expect: **toast pops at the bottom instantly**, the **bell badge increments**, and a row lands in `notifications`.
4. `docker compose logs -f reverb` shows the connection + message; `queue` logs show the broadcast job processed.

Then run formatting + the test suite:
```bash
docker compose exec app vendor/bin/pint --dirty --format agent
docker compose exec app php artisan test --compact
```

---

## 12. File-change summary

| File | Change |
| --- | --- |
| `composer.json` / lock | + `laravel/reverb` |
| `package.json` / lock | + `laravel-echo`, `pusher-js` |
| `docker-compose.yml` | complete `reverb` env; **add `queue` service**; add Reverb env to `app` |
| `deployment/docker/nginx/conf.d/app.conf` | *(optional)* `/app` ws proxy |
| `config/broadcasting.php`, `config/reverb.php` | published by installer |
| `bootstrap/app.php` | register `channels:` route |
| `routes/channels.php` | new — `App.Models.User.{id}` auth |
| `database/migrations/*_create_notifications_table.php` | new — **`uuidMorphs`** |
| `app/Notifications/ResourceNotification.php` | new — database + broadcast |
| `resources/js/app.js` | Echo init + `.notification()` → bottom toast + Livewire dispatch |
| `resources/views/partials/head.blade.php` | `<meta name="user-id">` |
| `resources/views/livewire/⚡notification-bell.blade.php` | new — live bell + dropdown |
| `resources/views/layouts/app/switchable-top.blade.php` | static bell → `<livewire:notification-bell>` (desktop + mobile) |
| `resources/views/layouts/app/switchable-side.blade.php` | static bell → `<livewire:notification-bell>` |
| `resources/views/layouts/app/switchable.blade.php` | *(optional)* `toast.group position="bottom center"` |
| `tests/Feature/NotificationBroadcastTest.php` | new |
| `.env` / `.env.example` | `BROADCAST_CONNECTION`, `REVERB_*`, `VITE_REVERB_*` |

---

## 13. Environment reference

```dotenv
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=local-app-id
REVERB_APP_KEY=local-app-key
REVERB_APP_SECRET=local-app-secret
REVERB_HOST=reverb        # server-side (compose service name); browser uses localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite — values the browser/Echo uses (host is localhost in dev)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST=localhost
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http
```

> Generate non-trivial credentials for any shared/staging environment; the values above are placeholders for local dev.

---

## 14. Open decisions

1. **WebSocket exposure** — direct port `8080` (default in this plan) vs nginx `/app` same-origin proxy (§4.4).
2. **Queue worker vs `ShouldBroadcastNow`** — dedicated `queue` container (recommended) vs synchronous broadcast for simplicity (§4.2).
3. **Popup position** — bottom-right (current default) vs bottom-center (§8.3).
4. **Reverb scaling** — enable `REVERB_SCALING_ENABLED` over the existing Redis if running multiple Reverb instances (not needed for single-node local).
5. **Notification taxonomy** — single generic `ResourceNotification` (this plan) vs per-event notification classes; revisit when concrete triggers (user created, role assigned, etc.) are defined.
