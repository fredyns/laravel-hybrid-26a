# Laravel Vertical-Slice Architecture Rules

Feature-oriented / vertical-slice architecture under `app/Features`, with **Livewire 4 + Flux UI** as the blessed UI stack. Goal: keep all code for one business use case together instead of splitting it across technical layers.

Core principles:

> **Code that changes together lives together.**
> **Organize by business capability and use case, not by technical layer.**
> **Every entry point — Livewire component, controller, command, job, schedule, webhook, listener — is a thin adapter into a use case, never the business logic.**
> **Direct calls inside a Feature; Laravel Events between Features.**
> **Keep use-case code local; promote shared code only when reuse is real.**

## 1. Status and scope

- Adopted incrementally. **Migrated:** `User` (web + API + purge schedule), `Notification` (page + API + cross-feature listener), `S3`, `Rbac` (Roles + Permissions + MyAccess; role URLs under `/rbac`, `/my-access` unchanged), `Enum` (generated enum lookup API). **Not yet migrated:** Settings, Auth.
- A Feature may group related areas one level deeper when the domain calls for it (`Rbac/Roles/<UseCase>`, `Rbac/Permissions/<UseCase>`); registration files and shared components then live at the Feature root (`Rbac/web-routes.php`, `Rbac/RoleResource.php`).
- Unmigrated code under `app/Http`, `app/Livewire`, `app/Actions`, `app/Services` **stays valid** — follow local conventions there. Do not mass-migrate.
- **New features go under `app/Features`** following these rules.
- The bridge is `app/Features/FeatureServiceProvider.php` (see §9), registered in `bootstrap/providers.php`.

## 2. UI stack

- **Livewire 4** for interactive pages/forms; inside Features use **class-based components** (§4). SFC/MFC (`⚡` under `resources/views`) is fine for shared non-feature UI.
- **Flux UI** (incl. Pro) is the component library — `<flux:*>` in feature views. Personally licensed: `composer.fluxui.dev` credentials required for `composer install`. Overrides in `resources/views/flux/`.
- **Alpine.js** (ships with Livewire) for client-only behavior. Tailwind v4 (CSS-first in `resources/css/app.css`), Vite, Echo/Reverb.
- Do not introduce Inertia, React, Vue, or Volt.

## 3. Structure and naming

Top-level folder = business capability (`Order`, `Invoice`). Subfolder = one use case / vertical slice (`Create`, `Approve`, `Import`). Everything used by exactly one slice lives in that slice folder:

```text
app/Features/Order/Create/
├── CreateOrderPage.php          // Livewire page (web adapter)
├── create-order-page.blade.php  // its view (Flux)
├── CreateOrderApiController.php // API adapter
├── CreateOrderRequest.php       // validation, single source (§5)
├── CreateOrder.php              // the use case — business logic
├── script.js / style.css        // optional (§6)
```

- **No extra layers** (`Modules/`, `Domains/`, `Application/`, `Infrastructure/`) and **no technical subfolders** inside a feature (`Controllers/`, `Services/`, `Requests/`, `Jobs/`) unless file count truly demands grouping. Keep slice folders flat.
- **Structure by use case, not transport.** `Order/Approve/`, never `Order/Http/`, `Order/Jobs/`.
- **Do not mirror URLs.** `/orders/{order}/approve` → `Order/Approve/`; `/admin/orders/...` needs no `Admin/` feature if the use case is still `Order/Approve`.
- **Names are business-oriented**, never generic (`Request.php`, `Service.php`, `Helper.php` — ambiguous across dozens of slices):

| Role | Pattern | Example |
|---|---|---|
| Use case (action) | `<Verb><Model>` | `CreateOrder`, `ExpireOrders` |
| Livewire full-page component | `<Verb/Noun><Model>Page` | `CreateOrderPage` |
| Livewire embedded component | descriptive | `CreateOrderForm` |
| Web controller (static page) | `<Verb><Model>WebController` | `ShowOrderWebController` |
| API controller | `<Verb><Model>ApiController` | `CreateOrderApiController` |
| Form request | `<Verb><Model>Request` | `CreateOrderRequest` |
| Job / Command | `<Verb><Model>Job` / `Command` | `ImportOrdersJob` |
| Event / Listener | past-tense fact / `<Event>Listener` | `OrderCreated`, `OrderCreatedListener` |

Fixed filenames: `view.blade.php` (controller-rendered slice), `script.js`, `style.css`, and the registration files of §9. Livewire views are kebab-case of their component (`create-order-page.blade.php`).

## 4. Adapters

| Situation | Adapter |
|---|---|
| Interactive page (forms, live search/sort, modals) | Livewire page via `Route::livewire()` |
| Static read-only page | Controller + Blade view |
| Machine consumer / integration | API controller + FormRequest + Resource |
| Slow, retryable, background work | Queue Job → use case |
| Time-driven work | Command + `schedules.php` |
| Third-party callback | Webhook controller (register in `api-routes.php`) |
| Reaction to another Feature's fact | Event Listener (§8) |

All adapters stay thin: bind/receive input, authorize, validate, call the use case, respond. Web and API adapters for the same operation **call the same action** — never duplicate business logic. Feature controllers are plain invokable classes (add `AuthorizesRequests` when needed); they do not extend `App\Http\Controllers\Controller`. Jobs/Commands keep a one-line `handle()` delegating to the action and live beside it.

```php
class CreateOrderApiController
{
    public function __invoke(CreateOrderRequest $request, CreateOrder $action): JsonResponse
    {
        return response()->json(['order' => OrderResource::make($action->execute($request->validated()))], 201);
    }
}
```

### Livewire components (verified against Livewire 4.3)

```php
#[Title('Create order')]
class CreateOrderPage extends Component
{
    use AuthorizesRequests;

    public string $reference = '';

    public function save(CreateOrder $action): void
    {
        $this->authorize('orders.create');
        $validated = $this->validate(CreateOrderRequest::rulesFor());
        $order = $action->execute($validated);

        Flux::toast(variant: 'success', text: __('Order created.'));
        $this->redirectRoute('orders.show', $order);
    }

    public function render(): View
    {
        return view('features::Order.Create.create-order-page');
    }
}
```

- **Full-page components need no registration** — route by class: `Route::livewire('orders/create', CreateOrderPage::class)->middleware('can:orders.create')->name('orders.create')`. Default layout `layouts::app`; override with `#[Layout]`, set title with `#[Title]`.
- **Embedded components** resolve via the `features::` Livewire namespace (§9): `<livewire:features::order.create.create-order-form />` → `App\Features\Order\Create\CreateOrderForm`.
- `$this->dispatch()` events are **UI signals** (toast, refresh, close-modal) — never cross-feature business meaning (that is §8).

**Livewire state is client input** — public properties and action arguments are browser-mutable; treat them exactly like request input:

- **Authorize inside every state-changing action**, even with `can:` route middleware (middleware re-applies on component requests, but cannot cover per-record decisions from mutable state).
- **Identity must be typed or locked:** `public Order $order` (Livewire locks the key) or `#[Locked]` on plain IDs. Never trust a bare `public $orderId`.
- **Whitelist user-selectable values** used to build queries (sort column, direction, per-page, filters) — see `IndexUsersPage::sortBy()`.
- **Every public method is a client-invokable endpoint** — helpers stay `private`/`protected`.
- **Public properties serialize to the browser** — no secrets, no Eloquent collections/query results; query in `render()` or `#[Computed]`.

**Index pages** keep UI state in `#[Url]` properties (`#[Url(as: 'q')] $search`, `sort`, `direction`, `#[Url(as: 'per_page')] $perPage`) so URLs are shareable and plain HTTP tests work. Use `WithPagination` and `resetPage()` on search/per-page/sort changes.

**Feature views:** `wire:submit="save"` + `wire:model` (deferred by default; `.live.debounce.300ms` only for reactive state like search). `<flux:input wire:model>` renders its own errors — no `@error` blocks. Every `@foreach` row gets `wire:key`. In-app links use `wire:navigate`. Livewire already disables submit buttons while submitting — add `wire:loading` only where the wait is noticeable.

**Performance features** (`@island`, `wire:click.async`/`#[Async]`, `lazy`/`defer`) are page-level opt-ins when measurably needed; they never change slice boundaries or move logic out of the use case.

## 5. One validation source per slice

Rules are declared **once**, as a static on the slice's FormRequest, with context passed explicitly (never `$this->route()` inside the static):

```php
class UpdateOrderRequest extends FormRequest
{
    public function rules(): array { return self::rulesFor($this->route('order')); }

    /** @return array<string, array<int, mixed>> */
    public static function rulesFor(?Order $order = null): array
    {
        return ['reference' => ['required', 'string', Rule::unique('orders')->ignore($order)]];
    }
}
```

API uses `$request->validated()`; Livewire uses `$this->validate(UpdateOrderRequest::rulesFor($this->order))`. A slice with no API may keep rules on the component until a second consumer appears. **Do not use `Livewire\Form` objects** — they become a second rules source.

## 6. Views and assets

- `FeatureServiceProvider` registers `app/Features` as the `features::` **view namespace**; dots map to directories: `view('features::Order.Create.create-order-page')`. Folder names must contain no dots; segments are PascalCase (case-sensitive on Linux). Layouts, shared Blade components, and Flux components stay global.
- Note `features::` serves **two** registries: Blade views (PascalCase) and Livewire components (kebab-case).
- A slice may own `script.js` / `style.css`, wired in three places: (1) `vite.config.js` globs them via `globSync(['app/Features/**/script.js', 'app/Features/**/style.css'])` into `input`, plus `'app/Features/**'` in `refresh`; (2) the view calls `@vite(['app/Features/Order/Index/style.css'])`; (3) `resources/css/app.css` contains `@source '../../app/Features';` — **without it production builds purge every class used only in feature views**.
- Adding or renaming a slice asset requires `npm run build` (or a running dev server) before the page/tests resolve — the manifest is keyed by exact input path. Requires Node ≥ 22 (`fs.globSync`).
- Prefer Livewire/Alpine over custom `script.js`.

## 7. Sharing, dependencies, authorization

**Promotion:** used by one slice → keep it there; used by several slices of one Feature → move to the feature parent (`Order/CalculatePricing.php`, `Order/OrderResource.php`); used across Features → find the right shared business concept. Never dump into global `Services/`, `Helpers/`, `Utils/`, `Common/`. Never copy business logic between slices to preserve isolation.

**Dependency direction:** adapter → use case → feature-local shared component → framework/model. Parent-level components must never import from a child slice.

**Authorization:** spatie permissions are the default — `can:orders.create` middleware and `$this->authorize('orders.create')` resolve without any `Gate::define`; permissions are seeded via `app/DB/Base*Migration`. `gates.php` exists only for **custom** abilities (e.g. model-state checks), defined in the owning slice. Blade keeps `@can('orders.update', $order)`.

**Transactions:** wrap operations that must succeed or fail together. Dispatch cross-feature events only after state is valid; use after-commit behavior (`ShouldQueueAfterCommit`, `afterCommit()`) so a queued listener never sees uncommitted or rolled-back state.

## 8. Cross-feature communication (Events)

A Feature must never import another Feature's internals (`use App\Features\Invoice\Create\CreateInvoice;` from inside `App\Features\Order\...` is forbidden). Publish a domain event instead — `OrderCreated::dispatch($order)` — and let the consumer listen. The producer never knows who consumes.

- **Events are facts, past tense:** `OrderCreated`, `PaymentReceived`. Never command-like (`CreateInvoice`, `SendEmail`).
- **Ownership:** the Event lives at `<Feature>/Events/OrderCreated.php`; the Listener lives in the *consuming* Feature's use case, with its `events.php`.
- **Sync vs queued:** sync when lightweight or needing immediate consistency; `ShouldQueue` when expensive, external, or fire-and-forget. Queued listeners serialize models — handle (or accept) model-deleted-before-processing. The choice never changes feature boundaries.
- **Not for internal calls:** inside one Feature, direct calls are clearer (`CreateOrder → CalculatePricing → CheckCreditLimit`).

## 9. Registration and FeatureServiceProvider

`app/Features/FeatureServiceProvider.php` is the only bridge between Laravel and `app/Features`. No per-feature providers, no business logic inside it. It discovers colocated registration files recursively (Symfony Finder, `sortByName()` for deterministic order):

| File | Contract |
|---|---|
| `web-routes.php` | routes; provider wraps in `Route::middleware('web')->group(...)` |
| `api-routes.php` | routes; provider wraps in `Route::middleware('api')->prefix('api')->group(...)` |
| `gates.php` | `Gate::define(...)` — custom abilities only (§7) |
| `events.php` | `Event::listen(Event::class, Listener::class)` |
| `commands.php` | **returns** `list<class-string>` of Commands |
| `schedules.php` | `Schedule::command(...)` facade calls |

```php
public function boot(): void
{
    $this->loadViewsFrom(app_path('Features'), 'features');
    Livewire::addNamespace('features', classNamespace: 'App\\Features', classPath: app_path('Features'));

    $this->registerRoutes();      // skipped when routesAreCached()
    $this->requireAll('gates.php');
    $this->requireAll('events.php');
    $this->registerCommands();    // console only; merges returned arrays into $this->commands()
    $this->registerSchedules();   // console only; callAfterResolving(Schedule::class, ...)
}
```

- Route files contain **no closures**, so `route:cache` stays possible.
- `event:cache` only affects auto-discovery; explicit `Event::listen` is unaffected.
- Actions, calculators, mappers and other internal classes need **no registration** — the container resolves them by type-hint.

## 10. Tests mirror features

`tests/Feature/<Feature>/<UseCase>Test.php` (e.g. `tests/Feature/Order/CreateTest.php`).

- Web and API cases for one slice live in the **same file**.
- Livewire pages: `Livewire::test(CreateOrderPage::class)` **plus** one HTTP smoke assertion (`$this->get(route('orders.create'))->assertOk()`) so routing/middleware stay covered.
- Cover authorization on both transports: page `assertForbidden()` without permission; API `assertUnauthorized()` (guest) and `assertForbidden()` (no permission).
- `#[Url]` state makes list behavior testable over plain HTTP: `$this->get(route('users.index', ['q' => 'jane']))`.
- Every interactive slice gets at least one tampered-input case (unknown sort column ignored, unauthorized action rejected).
- Shared Pest helpers live in `tests/Pest.php`, never duplicated. Use factories; `RefreshDatabase` applies to `tests/Feature`.

## 11. Restraint

- **No premature abstraction.** No interfaces, repositories, or managers to satisfy a pattern. Extract only for a concrete need: multiple implementations, real reuse, a real testing/integration boundary, or complexity already present. A one-statement use case may stay in its adapter — add the action class when logic exists.
- **Keep Laravel primitives.** This architecture changes file ownership and locality, not the framework: Form Requests, Gates, Events, Queues, Scheduler, Eloquent, Middleware, Route Model Binding, Notifications, named routes, Livewire test utilities all stay.

## 12. Checklist for AI-generated changes

1. Which Feature owns the behavior, and which use case performs it?
2. Is the file business logic, a shared feature component, or a thin adapter?
3. Which adapter fits (§4) — and is it staying thin?
4. Livewire slice: is client-mutable state treated as untrusted — authorized in the action, identity typed/`#[Locked]`, selectable values whitelisted (§4)?
5. Is the component used by one slice, several in one Feature, or several Features (§7)?
6. Does cross-feature communication need a domain event, not a Livewire `dispatch()` (§8)?
7. Does the new file need registration (route, embedded Livewire name, command, schedule, listener) or resolve automatically (§9)?
8. Could ordering, transaction timing, queue retries, or idempotency affect correctness (§7)?
9. Are validation rules declared exactly once (§5)?

Preserve project conventions that are more specific than this document. If a request conflicts with these rules, explain the conflict and choose the smallest safe deviation unless directed otherwise.

## 13. Reference structure

```text
app/Features/
├── FeatureServiceProvider.php       // the single bridge (§9)
├── Order/
│   ├── Events/OrderCreated.php
│   ├── OrderResource.php            // shared by several slices
│   ├── CalculatePricing.php         // shared feature-local logic
│   ├── web-routes.php / api-routes.php / gates.php
│   ├── Index/
│   │   ├── IndexOrdersPage.php + index-orders-page.blade.php
│   │   ├── IndexOrdersApiController.php
│   │   └── script.js / style.css    // optional
│   ├── Create/
│   │   ├── CreateOrderPage.php + create-order-page.blade.php
│   │   ├── CreateOrderApiController.php
│   │   ├── CreateOrderRequest.php
│   │   └── CreateOrder.php          // the use case
│   ├── Show/
│   │   ├── ShowOrderWebController.php + view.blade.php
│   │   └── ShowOrderApiController.php
│   └── Expire/
│       ├── ExpireOrders.php + ExpireOrdersJob.php + ExpireOrdersCommand.php
│       └── commands.php / schedules.php
└── Invoice/CreateFromOrder/
    ├── OrderCreatedListener.php + CreateInvoiceFromOrder.php
    └── events.php

tests/Feature/Order/{IndexTest,CreateTest,ShowTest,ExpireTest}.php
tests/Feature/Invoice/CreateFromOrderTest.php
```
