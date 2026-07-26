# 1. Repo structure & how to work with it

This app is **one Laravel project serving two frontends from a single origin** (`http://localhost:9900`):

1. **Server-rendered web app** — MVC pages with Flux UI (`/dashboard`, `/users`, `/roles`,
   `/login`, …): one invokable controller per page rendering a Blade view. Livewire still powers
   the settings pages (`/settings/*`) and interactive islands. Session-based auth via Fortify.
2. **React SPA** — a static single-page app served at `/` with hash routing
   (`/#/dashboard`, `/#/users`, …). Token-based auth via Sanctum against the REST API at `/api/*`.

Both frontends implement the same features against the same database and the same RBAC permissions.

## How a request is served

```
Browser ──► nginx (hybrid26a_web, :9900)
              │  root = public/,  index = index.html BEFORE index.php
              │
              ├─ "/"                → public/index.html   (built React SPA — static)
              ├─ "/spa/*.js|css"    → public/spa/         (SPA chunks — static)
              ├─ "/build/*"         → public/build/       (blade assets — static)
              └─ everything else    → public/index.php    (Laravel)
                                        ├─ /api/*      REST API   (auth:sanctum, bearer token)
                                        ├─ /dashboard  Blade      (session)
                                        ├─ /users …    Blade      (session)
                                        ├─ /settings/* Livewire   (session)
                                        └─ /docs/api   OpenAPI UI (Scramble, auto-generated)
```

The SPA never needs server-side rewrites because all its routes live behind `#` (hash history).
`/users` (no hash) is the server-rendered page; `/#/users` is the React page.

nginx config: `deployment/docker/nginx/conf.d/app.conf`.

## Directory map

```
app/
  DB/                          Reusable migration base classes:
                                 BasePermissionMigration      – declare permissions, auto-grant to roles/guards
                                 BaseInsertCollectionMigration– seed a table from database/collections/*.php
  Enums/                       UserRole (super-admin/internal/external), AuthGuard (web/sanctum)
  Concerns/                    Shared validation rule traits (ProfileValidationRules, PasswordValidationRules)
  Http/
    Controllers/<Feature>/     Server-rendered web pages — ONE INVOKABLE CONTROLLER PER PAGE:
      Users/                     UserIndexController, UserShowController, …
      Roles/                     RoleIndexController, RoleShowController, …
    Controllers/Api/           REST API — ONE INVOKABLE CONTROLLER PER ACTION:
      Auth/                      LoginController, RegisterController, LogoutController, MeController
      Users/                     IndexController, StoreController, ShowController, UpdateController,
                                 DestroyController, ChangePasswordController
      Roles/  Notifications/  Settings/   (same pattern)
    Requests/Api/<Feature>/    FormRequests (validation only; authorization is route middleware)
    Resources/                 Eloquent API Resources (UserResource, RoleResource, …)
  Livewire/Pages/Settings/     Class-based Livewire components (settings pages, modals)
  Models/                      Eloquent models. Conventions: uuid keys (HasUuids), #[Fillable([...])]
                               attribute, Searchable trait with $searchableFields
  Models/RBAC/                 Role & Permission (spatie models extended with uuid + search)

database/
  migrations/                  Schema AND data migrations (permissions + seeds are migrations here)
  collections/                 Seed data as plain PHP arrays (used by BaseInsertCollectionMigration)
  factories/                   Model factories for tests

routes/
  web.php                      Server-rendered pages (auth + verified; authorize() in controllers)
  api.php                      REST API (auth:sanctum + can: middleware, named api.*)
  settings.php                 Livewire settings pages

resources/
  css/app.css  js/app.js       Blade/Livewire assets (Tailwind v4, @source-scoped to views)
  views/
    layouts/app/               The hybrid layout: switchable-side / switchable-top (+ layout_mode cookie)
    <feature>/                 Blade views for the web pages (users/, roles/, … — Flux UI components)
    livewire/pages/settings/   Blade views for the Livewire settings components
    partials/head.blade.php    @vite entry for the blade side

frontend/                      React SPA (TypeScript). Mirrors features by folder:
  main.tsx  router.tsx         Entry + TanStack Router route tree (hash history)
  index.html                   Vite entry html (becomes public/index.html on build)
  index.css                    SPA Tailwind build (source-scoped to frontend/)
  lib/                         api.ts (fetch wrapper, bearer token, 401 handling), query.ts,
                               auth-storage.ts, appearance.ts (dark mode), layout-mode.ts
  components/ui/               Shared primitives (button, input, dialog, dropdown, table, badge…)
  features/<feature>/          <feature>.api.ts (typed fetchers) + use<Feature>.ts (react-query hooks)
                               + Page/Dialog components
  features/app-shell/          Sidebar/topbar shells, nav config (nav.ts), user menu

deployment/docker/nginx/       nginx config (the index.html-vs-index.php priority lives here)
tests/
  Pest.php                     Suite config + global helpers: apiAdminUser(), apiPlainUser()
  Feature/                     Web page tests (UsersTest, RolesTest, …)
  Feature/Api/                 API tests (UsersApiTest, RolesApiTest, …)
tutorial/                      You are here.
```

## Daily workflow

### PHP — always through docker

The host PHP is too old for this project; the container has PHP 8.5:

```bash
docker exec hybrid26a_app php artisan migrate
docker exec hybrid26a_app php artisan route:list --path=api
docker exec hybrid26a_app php artisan test --compact --filter=UsersApi
docker exec hybrid26a_app php artisan tinker --execute 'App\Models\User::count();'
docker exec hybrid26a_app ./vendor/bin/pint --dirty      # format changed PHP files (required before finishing)
```

### Frontend — on the host

```bash
npm run build          # typecheck + blade assets (public/build) + SPA (public/index.html, public/spa)
npm run dev            # Vite dev server for BLADE assets (Livewire pages hot-reload)
npm run dev:frontend   # Vite dev server for the SPA on :5173 (set VITE_API_URL for cross-origin)
npm run typecheck      # tsc over frontend/ only
```

In production-style serving (`:9900/`), the SPA is whatever was last **built** — rebuild to see SPA
changes there. For rapid SPA iteration use `npm run dev:frontend` on `:5173`.

### Tests

- Pest, suite-wide `RefreshDatabase`; `tests/TestCase.php` forces **sqlite `:memory:`**, so tests run
  in-container with zero setup and migrations (including permission/seed migrations) run per test run.
- Global helpers in `tests/Pest.php`: `apiAdminUser()` (super-admin on both guards),
  `apiPlainUser()` (no roles).
- API tests authenticate with `$this->actingAs($user, 'sanctum')`.
- Run the *narrowest* filter that covers your change: `php artisan test --compact --filter=TasksApi`.

## Auth & RBAC — the important mental model

Two parallel auth systems share one user table:

| | Web pages (Blade) | React SPA / API |
|---|---|---|
| Guard | `web` (session cookie) | `sanctum` (bearer token) |
| Login | Fortify `/login` | `POST /api/auth/login` → token |
| Permission check | `authorize()` in controllers + `@can` in blades | `can:` middleware on API routes + `useCan()` in React |

**Permissions are data, created by migrations.** A permission migration extends
`App\DB\BasePermissionMigration` and declares names once — the base class creates them **for both
guards** (`web` for the server-rendered pages, `sanctum` for the API) and grants them to roles:

```php
public function permissions(): array
{
    return [
        'tasks.index' => [UserRole::INTERNAL],  // granted to internal AND super-admin
        'tasks.create',                          // granted to super-admin only
    ];
}
```

Roles: `super-admin`, `internal`, `external` (see `App\Enums\UserRole`) exist once per guard and are
**protected** (cannot be renamed/deleted). Assign a role to a user on *both* guards with the model
helper: `$user->addRoleName('super-admin')` — plain spatie `assignRole()` only touches the `web` guard.
After changing permissions in tinker, clear the cache:
`app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();`

On the React side, `GET /api/user` returns the user's sanctum-guard `roles` and `permissions`; the
`useCan()` hook (`frontend/features/auth/useAuth.ts`) gates nav items, buttons and row actions.
Client-side gating is cosmetic — the API middleware is the real enforcement.

## API documentation

Scramble generates OpenAPI docs from the controllers automatically:
- UI: http://localhost:9900/docs/api
- JSON: http://localhost:9900/docs/api.json

New API routes show up there with no extra work (type hints + FormRequests are the source of truth).

## "I want to add X" — where things go

| Task | Files |
|---|---|
| New table | `database/migrations/…_create_x_table.php` |
| New permissions | `database/migrations/…_add_x_permissions.php` extending `BasePermissionMigration` |
| Seed rows | `database/collections/x.php` + migration extending `BaseInsertCollectionMigration` |
| New model | `app/Models/X.php` (+ factory) — uuid, `#[Fillable]`, `Searchable` |
| Web page | `app/Http/Controllers/X/<X…>Controller.php` (invokable) + `resources/views/x/…` + `routes/web.php` + nav in `resources/views/layouts/app/*.blade.php` |
| API endpoint | `app/Http/Controllers/Api/X/<Action>Controller.php` (invokable) + Request + Resource + `routes/api.php` |
| SPA page | `frontend/features/x/…` + route in `frontend/router.tsx` + nav item in `frontend/features/app-shell/nav.ts` |
| Tests | `tests/Feature/XTest.php` (web pages) and `tests/Feature/Api/XApiTest.php` (API) |

The CRUD series ([part 1 →](02-crud-database.md)) walks through every row of this table with a
complete working example.
