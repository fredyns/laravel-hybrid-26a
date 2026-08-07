# Laravel Vertical-Slice Architecture Rules

This project uses a **feature-oriented / vertical-slice architecture** under `app/Features`, with **Livewire 4 + Flux UI** as the blessed UI stack.

The main goal: keep all code related to a business use case close together, instead of organizing primarily by technical layers (global `Controllers`, `Services`, `Policies`, `Requests`, `Jobs`, `Listeners` directories).

Core principles (details in the numbered rules):

> **Code that changes together lives together.**

> **Organize by business capability and use case, not by technical layer.**

> **Every entry point — Livewire component, HTTP controller, CLI command, queue job, schedule, webhook, event listener — is a thin adapter into a use case, never the business logic itself.**

> **Use direct calls inside a Feature; use Laravel Events between top-level Features.**

> **Keep use-case-specific code local. Promote shared code only when reuse is real.**

These rules are for both developers and AI coding assistants working in this codebase.

## 1. Status and scope

- This document describes the **target architecture**, adopted incrementally. The pilot is **implemented**: the **User** feature (web + API + purge schedule) and the **Notification** consumer feature live under `app/Features/`, wired through `app/Providers/FeatureServiceProvider.php`.
- Until an area is migrated, its existing code under `app/Http`, `app/Livewire`, `app/Actions`, `app/Services` **remains valid** — follow the local conventions there. Do not mass-migrate. Still unmigrated: Roles, Notifications page/API, Settings, Auth, S3, Ajax.
- **New features go under `app/Features`** following these rules.
- Existing Livewire settings pages (`app/Livewire/Pages/Settings/*`) stay where they are until a Settings feature migration is planned.

## 2. UI stack

- **Livewire 4** renders interactive pages and forms. Inside Features, use **class-based components** (rule 8). SFC/MFC formats (`⚡` components under `resources/views`) remain fine for shared, non-feature UI.
- **Flux UI** (including Flux Pro) is the component library — use `<flux:*>` components in feature views. This kit is personally licensed; `composer.fluxui.dev` credentials are a prerequisite for `composer install`. Local Flux overrides live in `resources/views/flux/`.
- **Alpine.js** ships with Livewire — use it for purely client-side behavior instead of custom JS where possible.
- Tailwind v4 (CSS-first config in `resources/css/app.css`), Vite, Echo/Reverb for realtime.
- Do not introduce Inertia, React, Vue, or Volt.

## 3. Organize code by feature and use case

```text
app/
└── Features/
    └── Order/
        ├── Index/
        ├── Create/
        ├── Update/
        ├── Approve/
        └── ...
```

Top-level folders (`Order`, `Invoice`, `Customer`) represent a business capability, domain boundary, or resource area.

Subfolders (`Create`, `Update`, `Approve`, `Cancel`, `Import`, `Sync`) represent individual use cases — vertical slices.

Do not introduce extra architecture layers (`Modules/`, `Domains/`, `Application/`, `Infrastructure/`) unless a concrete need emerges.

## 4. Colocate use-case-specific components

Any component used exclusively by one use case lives inside that use-case folder:

```text
app/Features/Order/Create/
├── CreateOrderPage.php          // Livewire full-page component (web UI adapter)
├── create-order-page.blade.php  // its view (Flux components)
├── CreateOrderApiController.php // API adapter
├── CreateOrderRequest.php       // validation (single source, see rule 11)
├── CreateOrder.php              // the use case — business logic
├── script.js                    // optional page JS (rule 13)
└── style.css                    // optional page CSS (rule 13)
```

Do not create technical subdirectories (`Controllers/`, `Services/`, `Requests/`, `Policies/`, `Jobs/`, `Listeners/`) inside a feature unless file count makes grouping genuinely useful. Prefer a flat use-case folder while it remains easy to scan.

## 5. Organize by use case, not transport

The directory structure describes **what the application does**, not how the action is triggered.

Prefer:

```text
Order/
├── Create/
├── Approve/
├── Expire/
├── Import/
└── SyncMarketplace/
```

Avoid transport-centric structures:

```text
Order/
├── Http/
├── Livewire/
├── Jobs/
├── Commands/
└── Webhooks/
```

Livewire, HTTP, Queue, CLI, Schedule, Event, and Webhook are entry points into a use case — not the architectural boundary.

## 6. Naming

Class names are descriptive and business-oriented. Never use generic per-slice names (`Request.php`, `Policy.php`, `Service.php`, `Manager.php`, `Helper.php`) — with dozens of slices they make fuzzy-find, logs, and stack traces ambiguous.

| Role | Pattern | Example |
|---|---|---|
| Use case (action) | `<Verb><Model>` | `CreateOrder`, `ExpireOrders` |
| Livewire full-page component | `<Verb/Noun><Model>Page` | `CreateOrderPage`, `IndexOrdersPage` |
| Livewire embedded component | descriptive | `CreateOrderForm`, `OrderStatusBadge` |
| Web controller (static page) | `<Verb><Model>WebController` | `ShowOrderWebController` |
| API controller | `<Verb><Model>ApiController` | `CreateOrderApiController` |
| Form request | `<Verb><Model>Request` | `CreateOrderRequest` |
| Job | `<Verb><Model>Job` | `ImportOrdersJob` |
| Command | `<Verb><Model>Command` | `ExpireOrdersCommand` |
| Event | past-tense fact | `OrderCreated` |
| Listener | `<Event>Listener` | `OrderCreatedListener` |

Fixed filenames (never renamed): `view.blade.php` (single-view controller slice), `script.js`, `style.css`, and the registration files of rule 29. Livewire component views are named after their component in kebab-case (`create-order-page.blade.php`).

## 7. Choose the right adapter

| Situation | Adapter |
|---|---|
| Interactive page (forms, live search/sort, modals) | Livewire full-page component via `Route::livewire()` |
| Static read-only page | Plain controller + Blade view |
| Machine consumer / SPA / integration | API controller + FormRequest + Resource |
| Slow, retryable, background work | Queue Job delegating to the use case |
| Time-driven work | Command + `schedules.php` entry |
| Third-party callback | Webhook controller (HTTP adapter) |
| Reaction to another Feature's fact | Event Listener |

Not every page must be Livewire. A page with no interactivity is cheaper and simpler as controller + Blade.

## 8. Livewire components are adapters

A Livewire component handles UI concerns only: bind input, validate, call the use case, redirect/render. Business logic lives in the action.

```php
namespace App\Features\Order\Create;

use Livewire\Component;

class CreateOrderPage extends Component
{
    public string $reference = '';

    public function save(CreateOrder $action)
    {
        $validated = $this->validate(CreateOrderRequest::rulesFor());

        $order = $action->execute($validated);

        return $this->redirectRoute('orders.show', $order);
    }

    public function render()
    {
        return view('features::Order.Create.create-order-page');
    }
}
```

Conventions (verified against Livewire 4.1 in `vendor/`):

- **Full-page components need no registration.** Route them by class in the slice's `web-routes.php`:

  ```php
  Route::livewire('orders/create', CreateOrderPage::class)
      ->middleware('can:orders.create')
      ->name('orders.create');
  ```

  The default layout is `layouts::app` (`config('livewire.component_layout')`); override per component with `#[Layout]` when needed.
- **Embedded components** (`<livewire:...>` inside another view) resolve through the `features::` component namespace registered by `FeatureServiceProvider` (rule 29). The name maps dot-segments to StudlyCase classes: `<livewire:features::order.create.create-order-form />` → `App\Features\Order\Create\CreateOrderForm`.
- Components stay **class-based** inside Features (official Livewire option for "established conventions around class-based architecture"). Views are colocated and rendered via the `features::` **view** namespace (rule 12).
- `$this->dispatch()` browser/Livewire events are **UI signals** (toast, refresh, close-modal). They are not domain events and never carry cross-feature business meaning — that is rule 23's job.

## 9. Keep controllers thin

Controllers (web and API) only handle HTTP concerns: receive the request, obtain validated input, invoke the use case, return a response.

```php
class CreateOrderApiController
{
    public function __invoke(CreateOrderRequest $request, CreateOrder $action): JsonResponse
    {
        $order = $action->execute($request->validated());

        return response()->json([
            'message' => 'Order created',
            'order' => OrderResource::make($order),
        ], 201);
    }
}
```

Feature controllers are plain invokable classes (add the `AuthorizesRequests` trait when needed) — they do not extend `App\Http\Controllers\Controller`.

## 10. Web and API entry points share the same use case

If the Livewire page and the API perform the same business operation, both call the same action:

```text
CreateOrderPage ──┐
                  ├── CreateOrder
CreateOrderApiController ──┘
```

Never duplicate business logic between adapters.

## 11. One validation source per slice

Validation rules are declared **once** per use case, as a static method on the slice's FormRequest, with context passed explicitly (never via `$this->route()` inside the static):

```php
class UpdateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return self::rulesFor($this->route('order'));
    }

    /** @return array<string, array<int, mixed>> */
    public static function rulesFor(?Order $order = null): array
    {
        return [
            'reference' => ['required', 'string', Rule::unique('orders')->ignore($order)],
        ];
    }
}
```

- API adapters use the FormRequest normally (`$request->validated()`).
- Livewire components consume the same source: `$this->validate(UpdateOrderRequest::rulesFor($this->order))`.
- A slice with no API may keep rules directly on the Livewire component — introduce the static only when a second consumer appears.

## 12. Colocated views

`FeatureServiceProvider` registers `app/Features` as the `features::` **view namespace**. Dots map to directories:

```php
view('features::Order.Create.create-order-page')
// → app/Features/Order/Create/create-order-page.blade.php
```

Constraints:

- Feature and slice folder names must never contain dots.
- View names are case-sensitive on Linux — path segments are PascalCase exactly matching the folders.
- Layouts (`<x-layouts::app>`), shared Blade components, and Flux components remain global and work unchanged from feature views.

Note the distinction: `features::` is used by **two** registries — the Blade view namespace (`view('features::Order.Create...')`, PascalCase) and the Livewire component namespace (`<livewire:features::order.create...>`, kebab-case). Same word, different resolvers.

## 13. Colocated assets

A slice may own page-level `script.js` / `style.css`. They are wired in three places:

1. `vite.config.js` globs them as entries:

   ```js
   import { globSync } from 'node:fs';

   const featureAssets = globSync(['app/Features/**/script.js', 'app/Features/**/style.css'])
       .map((p) => p.replaceAll('\\', '/'));
   // laravel({ input: [...defaultEntries, ...featureAssets], refresh: [...refreshPaths, 'app/Features/**'], ... })
   ```

2. The view loads them: `@vite(['app/Features/Order/Index/style.css', 'app/Features/Order/Index/script.js'])`.
3. `resources/css/app.css` must contain `@source '../../app/Features';` so Tailwind scans feature views — **without it, production builds purge every class used only in feature views.**

Prefer Livewire/Alpine over custom `script.js`; reach for colocated assets only when a page genuinely needs bespoke JS/CSS.

## 14. Promote shared components to the nearest common parent

```text
used by one slice
→ keep it inside that slice

used by multiple slices in the same feature
→ move it to the feature parent (e.g. Order/CalculatePricing.php, Order/OrderResource.php)

used across multiple top-level features
→ identify the correct shared abstraction or business concept
```

Do not preemptively move reusable code into global dumping grounds (`Services/`, `Helpers/`, `Utils/`, `Common/`).

## 15. Follow a clear dependency direction

```text
Adapter (Livewire component / controller / job / command / listener)
        ↓
     Use case (action)
        ↓
Feature-local shared component (Order/CalculatePricing.php)
        ↓
Framework / Model / shared infrastructure
```

Parent-level components must not depend on child slices:

```text
Avoid:  Order/CalculatePricing.php → Order/Create/*
```

A parent-level shared component stays reusable and never imports implementation details from a specific slice.

## 16. Do not mirror URL paths literally

Directories represent semantic use cases, not URL segments. For `/orders/{order}/approve` use `Order/Approve/`, not `Order/{order}/Approve/`. Likewise `/admin/orders/{order}/approve` does not require an `Admin/` feature if the business use case is still `Order/Approve`.

## 17. Authorization

- **Spatie permissions are the default story.** Route middleware `can:orders.create` and `$this->authorize('orders.create')` resolve through spatie/laravel-permission — permission-named abilities need **no** `Gate::define`. Permissions are seeded via the `app/DB/Base*Migration` classes.
- `gates.php` (rule 29) exists only for **custom** abilities that are not plain permissions (e.g. model-state checks). Keep such a Gate's class/callback in the slice that owns it:

  ```php
  Gate::define('order.approve', [\App\Features\Order\Approve\ApproveOrderPolicy::class, 'authorize']);
  ```

- Blade keeps using the normal API: `@can('orders.create') ... @endcan`, `@can('orders.update', $order)`.

## 18. Queue Jobs are adapters

Jobs need no global registration — dispatch normally (`ImportOrdersJob::dispatch($payload)`), keep `handle()` thin, store the Job next to its use case:

```text
Order/Import/
├── ImportOrders.php
└── ImportOrdersJob.php
```

```php
class ImportOrdersJob implements ShouldQueue
{
    public function handle(ImportOrders $action): void
    {
        $action->execute();
    }
}
```

## 19. Treat queues and schedules as triggers

A queue job or scheduled task is usually not a separate business use case — it is an adapter invoking one:

```text
Scheduler → Job → ExpireOrders
CLI Command ───→ ExpireOrders
Livewire page ─→ ExpireOrders
```

Use jobs for slow, retryable, or background work. Use `withoutOverlapping()` / `onOneServer()` on schedules that require them.

## 20. CLI Commands are adapters

```php
class ExpireOrdersCommand extends Command
{
    protected $signature = 'orders:expire {--days=30}';

    public function handle(ExpireOrders $action): int
    {
        $action->execute((int) $this->option('days'));

        return self::SUCCESS;
    }
}
```

Store the Command next to its use case and register it via the slice's `commands.php` (rule 29).

## 21. Webhooks are HTTP adapters

A webhook is an HTTP entry point. Store its controller next to the use case it triggers; register its route through the feature's `api-routes.php`:

```text
Order/SyncMarketplace/
├── SyncMarketplaceWebhookController.php
├── SyncMarketplaceRequest.php
└── SyncMarketplace.php
```

## 22. Use transactions around the atomic business change

Place persistence operations that must succeed or fail together inside a database transaction.

Dispatch cross-feature events only after the originating state is valid. When an event must not be processed before the transaction commits, use Laravel's after-commit behavior (`ShouldQueueAfterCommit`, `afterCommit()`). A queued listener must never observe uncommitted or rolled-back state.

## 23. Use Laravel Events for communication between top-level Features

**A Feature must not depend on another Feature's internals to trigger cross-feature side effects.**

Avoid:

```php
namespace App\Features\Order\Create;

use App\Features\Invoice\Create\CreateInvoice; // ← direct coupling

class CreateOrder
{
    public function __construct(private CreateInvoice $createInvoice) {}
}
```

Instead, publish a domain event:

```php
OrderCreated::dispatch($order);
```

The consuming Feature listens:

```text
Order ──▶ OrderCreated event ◀── Invoice listener
```

The producer never knows which Features consume the event.

## 24. Events represent facts that already happened

Use domain language, past tense: `OrderCreated`, `OrderApproved`, `PaymentReceived`, `InvoiceIssued`.

Never command-like names (`CreateInvoice`, `ProcessPayment`, `SendEmail`) — a command describes what should happen; an event describes what already did.

## 25. Event and Listener ownership

- The **Event** lives with the Feature that owns the fact, always at `<Feature>/Events/`:

  ```text
  Order/Events/OrderCreated.php
  ```

- The **Listener** lives in the Feature that reacts, inside its consuming use case:

  ```text
  Invoice/CreateFromOrder/
  ├── OrderCreatedListener.php
  ├── CreateInvoiceFromOrder.php
  └── events.php
  ```

## 26. Avoid direct cross-feature implementation dependencies

Code in one top-level Feature must not import internal classes of another (`use App\Features\Invoice\Create\CreateInvoice;` from inside `App\Features\Order\...` is forbidden).

To react to another Feature's fact → use an Event. If multiple Features truly need the same business logic → extract a shared business concept; never make Features call each other directly.

## 27. Event Listeners may be synchronous or queued

Synchronous when the operation is lightweight, must complete in the same flow, or requires immediate consistency. Queued (`implements ShouldQueue`) when it is expensive, calls external APIs, sends mail/notifications, generates files, or need not finish before the response.

The sync/queued choice never changes the Feature boundary. Note: a queued listener serializes its models; handle the model-deleted-before-processing case or accept the failure.

## 28. Do not use Events for every internal call

Events are for **cross-feature** communication and meaningful side effects. Inside one Feature, direct calls are clearer:

```text
Order/Create/CreateOrder → Order/CalculatePricing → Order/CheckCreditLimit
```

Do not replace explicit internal control flow with events.

## 29. Registration files and FeatureServiceProvider

One provider — `app/Providers/FeatureServiceProvider.php`, registered in `bootstrap/providers.php` — is the only bridge between Laravel and `app/Features`. No per-feature providers unless a concrete need appears. No business logic in the provider.

Colocated registration files, discovered recursively (Symfony Finder, `sortByName()` for deterministic order), each with a strict contract:

| File | Contract |
|---|---|
| `web-routes.php` | Route definitions; provider wraps the file in `Route::middleware('web')->group(...)` |
| `api-routes.php` | Route definitions; provider wraps in `Route::middleware('api')->prefix('api')->group(...)` |
| `gates.php` | `Gate::define(...)` calls (custom abilities only, rule 17) |
| `events.php` | `Event::listen(EventClass::class, ListenerClass::class)` calls |
| `commands.php` | **returns** `list<class-string>` of Command classes |
| `schedules.php` | `Schedule::command(...)` facade calls |

Provider skeleton:

```php
class FeatureServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(app_path('Features'), 'features');

        Livewire::addNamespace(
            namespace: 'features',
            classNamespace: 'App\\Features',
            classPath: app_path('Features'),
        );

        $this->registerRoutes();      // skips when $this->app->routesAreCached()
        $this->requireAll('gates.php');
        $this->requireAll('events.php');
        $this->registerCommands();    // console only; merges the returned arrays into $this->commands()
        $this->registerSchedules();   // console only; callAfterResolving(Schedule::class, ...)
    }
}
```

Operational notes:

- Route files contain **no closures** — class controllers / component classes only, so `route:cache` stays possible.
- `event:cache` only affects auto-discovered listeners; explicit `Event::listen` in `events.php` is unaffected.
- Discovery cost is a few sub-millisecond Finder scans; an optional `features:cache` command is a future optimization, not a current need.

## 30. Internal classes require no registration

Actions, calculators, rules, mappers, and processors resolve through the container by type-hint — no binding, no registration:

```php
public function handle(ApproveOrder $action): void
{
    $action->execute();
}
```

## 31. Tests mirror features

```text
tests/Feature/<Feature>/<UseCase>Test.php
tests/Feature/Order/CreateTest.php
```

- Web and API cases for the same slice live in the **same file** — both transports exercise one use case.
- Livewire pages are tested with `Livewire::test(CreateOrderPage::class)` **plus** one plain HTTP smoke assertion (`$this->get(route('orders.create'))->assertOk()`) so routing and middleware stay covered.
- Shared Pest helpers live in `tests/Pest.php`, never duplicated across test files.
- Use model factories; `RefreshDatabase` applies to everything under `tests/Feature`.

## 32. Avoid duplicated business logic

Never copy business logic between slices to preserve isolation. Shared by multiple slices in one Feature → promote to the feature parent (rule 14).

## 33. Do not introduce abstractions before they are needed

No interfaces, repositories, managers, or extra layers to satisfy a pattern. Start with the simplest implementation that expresses the use case. Extract an abstraction only for a concrete need: multiple implementations, real reuse, a real testing or integration boundary, or complexity that has already emerged. A one-statement use case may stay in its adapter — introduce the action class when logic exists.

## 34. Keep Laravel conventions where they remain useful

Feature organization changes **file ownership and locality** — it does not replace framework primitives. Keep using Form Requests, Gates, Events, Listeners, Queues, Scheduler, Eloquent, Middleware, Service Container, Route Model Binding, Validation, Notifications, named routes + `route()`, and Livewire's own testing utilities.

## 35. Rules for AI-generated changes

Before creating or moving code, determine:

1. Which Feature owns the behavior, and which use case performs it?
2. Is the file business logic, a shared feature component, or a thin adapter?
3. Which adapter fits (rule 7) — and is the Livewire component/controller staying thin?
4. Is the component used by one slice, multiple slices in one Feature, or multiple Features?
5. Does cross-feature communication require a domain event (not a Livewire `dispatch()`)?
6. Does the new file need registration (routes, embedded Livewire name, command, schedule, listener) or does it resolve automatically?
7. Could ordering, transaction timing, queue retries, or idempotency affect correctness?
8. Are validation rules declared exactly once for the slice (rule 11)?

Preserve existing project conventions when they are more specific than this document. If a requested change conflicts with these rules, explain the conflict and choose the smallest safe deviation unless the user directs otherwise.

## 36. Reference structure

```text
app/Features/
├── Order/
│   ├── Events/
│   │   └── OrderCreated.php
│   ├── OrderResource.php            // shared by Index/Show/Create/Update
│   ├── CalculatePricing.php         // shared feature-local logic
│   ├── web-routes.php
│   ├── api-routes.php
│   ├── gates.php                    // only if custom gates exist
│   ├── Index/
│   │   ├── IndexOrdersPage.php      // Livewire page: search/sort/paginate
│   │   ├── index-orders-page.blade.php
│   │   ├── IndexOrdersApiController.php
│   │   ├── script.js                // optional
│   │   └── style.css                // optional
│   ├── Create/
│   │   ├── CreateOrderPage.php
│   │   ├── create-order-page.blade.php
│   │   ├── CreateOrderApiController.php
│   │   ├── CreateOrderRequest.php
│   │   └── CreateOrder.php          // the use case
│   ├── Show/
│   │   ├── ShowOrderWebController.php   // static page: controller + Blade
│   │   ├── view.blade.php
│   │   └── ShowOrderApiController.php
│   ├── Approve/
│   │   └── ApproveOrder.php
│   └── Expire/
│       ├── ExpireOrders.php
│       ├── ExpireOrdersJob.php
│       ├── ExpireOrdersCommand.php
│       ├── commands.php
│       └── schedules.php
│
└── Invoice/
    └── CreateFromOrder/
        ├── OrderCreatedListener.php
        ├── CreateInvoiceFromOrder.php
        └── events.php

app/Providers/FeatureServiceProvider.php   // the single bridge (rule 29)

tests/Feature/
├── Order/
│   ├── IndexTest.php
│   ├── CreateTest.php
│   ├── ShowTest.php
│   └── ExpireTest.php
└── Invoice/
    └── CreateFromOrderTest.php
```
