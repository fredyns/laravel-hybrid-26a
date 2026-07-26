# Tutorials

Hands-on guides for working in this repository. Start with the structure guide, then follow the
CRUD series in order — it builds one feature (a simple **Tasks** resource) through every layer of
the stack, using the same patterns the existing **Users** and **Roles** features use.

| # | Guide | What you learn |
|---|---|---|
| 1 | [Repo structure & how to work with it](01-repo-structure.md) | Architecture, directory map, docker workflow, RBAC, testing, builds |
| 2 | [CRUD part 1 — Database](02-crud-database.md) | Table migration, permission migration, model + factory |
| 3 | [CRUD part 2 — Web pages](03-crud-web.md) | Invokable page controllers, Blade index/show views, web routes, sidebar nav, feature tests |
| 4 | [CRUD part 3 — REST API](04-crud-api.md) | Invokable controllers, FormRequests, API Resource, routes, Pest tests |
| 5 | [CRUD part 4 — React frontend](05-crud-frontend.md) | Typed API layer, react-query hooks, page + dialogs, router, RBAC gating |

## Prerequisites

- Docker stack running (`docker compose up -d` at the repo root) — containers are named `hybrid26a_*`,
  the app is served at **http://localhost:9900**.
- Node.js on the host for asset builds (`npm install` once).
- **All PHP commands run inside the app container** — the project needs PHP 8.5:

  ```bash
  docker exec hybrid26a_app php artisan <command>
  docker exec hybrid26a_app ./vendor/bin/pint --dirty
  ```

- Test login: `admin@admin.com` / `admin` (super-admin on both guards).

## The example resource

The CRUD series builds a **Task** — `title` (string), `notes` (nullable text), `done` (boolean) —
protected by five permissions (`tasks.index/show/create/update/delete`), with:

- a server-rendered Blade page at `http://localhost:9900/tasks` (MVC controllers, session auth), and
- a React SPA page at `http://localhost:9900/#/tasks` (token auth via `/api/tasks`).

Each part ends with a **Verify** section; don't skip those — they catch mistakes early.

## Paths through this tutorial

The parts are independent above the database layer — pick the path that fits the feature:

| Path | Parts | When |
|---|---|---|
| **Full stack** | 2 → 3 → 4 → 5 | The resource needs both frontends (like Users/Roles) |
| **API + SPA only** | 2 → 4 → 5 | The resource only lives in the React app — **part 3 is skippable** |
| **API only** | 2 → 4 | Headless / consumed by another client |

Rules that hold on every path:

- **Part 2 (database) is always required first**, including **all five permissions** — even when
  skipping the web page, the API middleware checks `tasks.*` on the `sanctum` guard, and
  `BasePermissionMigration` creates both guards in one migration anyway.
- Parts 4 and 5 have no dependency on part 3; ignore the "Next" link at the end of part 2 and go
  straight to [part 3 — REST API](04-crud-api.md) if you don't need the server-rendered page.
- Finish each part's **Verify** section before starting the next.

### File checklist — API + SPA path

Backend (part 2 + 4), all created inside the container with `php artisan make:…` where a generator exists:

```
database/migrations/…_create_tasks_table.php
database/migrations/…_add_tasks_permissions.php
database/factories/TaskFactory.php
app/Models/Task.php
app/Http/Resources/TaskResource.php
app/Http/Requests/Api/Tasks/StoreTaskRequest.php
app/Http/Requests/Api/Tasks/UpdateTaskRequest.php
app/Http/Controllers/Api/Tasks/IndexController.php
app/Http/Controllers/Api/Tasks/StoreController.php
app/Http/Controllers/Api/Tasks/ShowController.php
app/Http/Controllers/Api/Tasks/UpdateController.php
app/Http/Controllers/Api/Tasks/DestroyController.php
routes/api.php                      (5 routes added, named api.tasks.*)
tests/Feature/Api/TasksApiTest.php
```

Frontend (part 5), then `npm run build`:

```
frontend/features/tasks/tasks.api.ts
frontend/features/tasks/useTasks.ts
frontend/features/tasks/TaskFormDialog.tsx
frontend/features/tasks/TasksPage.tsx
frontend/router.tsx                 (route added)
frontend/features/app-shell/nav.ts  (tasksItem + breadcrumb branch added)
frontend/features/app-shell/Sidebar.tsx   (gated SidebarLink added)
frontend/features/app-shell/AppShell.tsx  (gated header link added)
```

### File checklist — add the web page (part 3)

```
app/Http/Controllers/Tasks/TaskIndexController.php
app/Http/Controllers/Tasks/TaskShowController.php
resources/views/tasks/index.blade.php
resources/views/tasks/show.blade.php
routes/web.php                      (2 routes added, named tasks.*)
resources/views/layouts/app/*.blade.php   (gated nav item per layout variant)
tests/Feature/TasksTest.php
```
