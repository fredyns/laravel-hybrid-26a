# Tutorials

Hands-on guides for working in this repository. Start with the structure guide, then follow the
CRUD series in order — it builds one feature (a simple **Tasks** resource) through every layer of
the stack, using the same patterns the existing **Users** and **Roles** features use.

| # | Guide | What you learn |
|---|---|---|
| 1 | [Repo structure & how to work with it](01-repo-structure.md) | Architecture, directory map, docker workflow, RBAC, testing, builds |
| 2 | [CRUD part 1 — Database](02-crud-database.md) | Table migration, permission migration, model + factory |
| 3 | [CRUD part 2 — Livewire app](03-crud-livewire.md) | Index page, form modal, web routes, sidebar nav, feature tests |
| 4 | [CRUD part 3 — REST API](04-crud-api.md) | Invokable controllers, FormRequests, API Resource, routes, Pest tests |
| 5 | [CRUD part 4 — React frontend](05-crud-frontend.md) | Typed API layer, react-query hooks, page + dialogs, router, RBAC gating |

## Prerequisites

- Docker stack running (`docker compose up -d` at the repo root) — containers are named `livewire26d_*`,
  the app is served at **http://localhost:9700**.
- Node.js on the host for asset builds (`npm install` once).
- **All PHP commands run inside the app container** — the project needs PHP 8.5:

  ```bash
  docker exec livewire26d_app php artisan <command>
  docker exec livewire26d_app ./vendor/bin/pint --dirty
  ```

- Test login: `admin@admin.com` / `admin` (super-admin on both guards).

## The example resource

The CRUD series builds a **Task** — `title` (string), `notes` (nullable text), `done` (boolean) —
protected by five permissions (`tasks.index/show/create/update/delete`), with:

- a Livewire page at `http://localhost:9700/tasks` (server-rendered, session auth), and
- a React SPA page at `http://localhost:9700/#/tasks` (token auth via `/api/tasks`).

Each part ends with a **Verify** section; don't skip those — they catch mistakes early.
