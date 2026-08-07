# Laravel Feature-Oriented Architecture Rules

This project uses a **feature-oriented / vertical-slice architecture** under `app/Features`.

The main goal is to keep all code related to a business use case close together instead of organizing application code primarily by technical layers such as global `Controllers`, `Services`, `Policies`, `Requests`, `Jobs`, or `Listeners` directories.

These rules are intended for both developers and AI coding assistants working in this codebase.

## 1. Organize code by feature and use case

Use:

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

Top-level folders such as `Order`, `Invoice`, and `Customer` represent a business capability, domain boundary, or resource area.

Subfolders such as `Create`, `Update`, `Approve`, `Cancel`, `Import`, and `Sync` represent individual use cases or vertical slices.

Do not introduce additional architecture layers such as:

```text
Modules/
Domains/
Application/
Infrastructure/
```

unless the project has a concrete need for them.

## 2. Colocate use-case-specific components

Any component used exclusively by one use case must live inside that use-case folder.

Example:

```text
app/Features/Order/Create/
├── WebController.php
├── ApiController.php
├── CreateOrder.php
├── Request.php
├── Policy.php
├── Job.php
├── Listener.php
└── view.blade.php
```

Do not create technical subdirectories such as:

```text
Controllers/
Services/
Requests/
Policies/
Jobs/
Listeners/
```

inside a feature unless the number of files or complexity makes that additional grouping genuinely useful.

Prefer a flat use-case folder while it remains easy to understand.

## 3. Organize by use case, not transport

The directory structure should describe **what the application does**, not how the action is triggered.

Prefer:

```text
Order/
├── Create/
├── Approve/
├── Expire/
├── Import/
└── SyncMarketplace/
```

Avoid structures centered around transport or framework mechanisms:

```text
Order/
├── Http/
├── Jobs/
├── Commands/
├── Events/
└── Webhooks/
```

HTTP, Queue, CLI, Schedule, Event, and Webhook are entry points or adapters into a use case.

They are not the primary architectural boundary.

## 4. Keep controllers thin

Controllers should only handle HTTP concerns such as:

- receiving the request;
- obtaining validated input;
- invoking the use case;
- returning a response, redirect, view, or resource.

Do not place core business logic in controllers.

Example:

```php
class ApiController
{
    public function __invoke(Request $request, CreateOrder $action)
    {
        $order = $action->execute($request->validated());

        return response()->json($order);
    }
}
```

## 5. Web and API entry points may share the same use case

If the Web and API interfaces perform the same business operation, both must use the same use-case implementation.

Example:

```text
Create/
├── WebController.php
├── ApiController.php
├── CreateOrder.php
├── Request.php
└── Policy.php
```

Expected dependency:

```text
WebController ─┐
               ├── CreateOrder
ApiController ─┘
```

Do not duplicate business logic between Web and API controllers.

## 6. Prefer explicit business-oriented class names

Prefer names such as:

```text
CreateOrder.php
ApproveOrder.php
CalculatePricing.php
ExpireOrders.php
SyncMarketplace.php
```

Avoid vague names such as:

```text
Service.php
Manager.php
Helper.php
Utils.php
BusinessRules.php
Common.php
```

A class name should communicate its responsibility.

Generic names are acceptable only when the namespace provides enough context and the name does not create ambiguity in code navigation, logs, debugging, or stack traces.

## 7. Promote shared components to the nearest common parent

If a component is used by only one use case, keep it inside that use case:

```text
Order/Create/CalculateSomething.php
```

If it becomes shared by multiple use cases within `Order`, move it to the nearest common parent:

```text
Order/
├── Create/
├── Update/
├── Approve/
├── CalculatePricing.php
├── CheckCreditLimit.php
└── GenerateOrderNumber.php
```

Use this rule:

```text
used by one slice
→ keep it inside that slice

used by multiple slices in the same feature
→ move it to the feature parent

used across multiple top-level features
→ identify the correct shared abstraction or business concept
```

Do not immediately move reusable code into global dumping-ground directories such as:

```text
Services/
Helpers/
Utils/
Common/
```

## 8. Parent-level components must not depend on child use cases

The preferred dependency direction is:

```text
Order/Create/*
      ↓
Order/*
      ↓
shared infrastructure / framework / model
```

Avoid:

```text
Order/CalculatePricing.php
      ↓
Order/Create/*
```

A parent-level shared component should remain reusable and must not depend on implementation details from a specific child use case.

## 9. Do not mirror URL paths literally

Directories should represent semantic actions and use cases, not every URL segment.

For:

```text
/orders/{order}/approve
```

use:

```text
Order/
└── Approve/
```

not:

```text
Order/
└── {order}/
    └── Approve/
```

Likewise, `/admin/orders/{order}/approve` does not automatically require:

```text
Admin/
└── Order/
    └── Approve/
```

if the actual business use case still belongs to `Order/Approve`.

## 10. Keep authorization close to the use case

If an authorization rule exists only for one use case, place its authorization class or callback in that use-case folder.

Example:

```text
Order/
└── Create/
    └── Policy.php
```

A Gate may map an ability to that class:

```php
Gate::define('order.create', [
    \App\Features\Order\Create\Policy::class,
    'authorize',
]);
```

An invokable authorization class may also be used when appropriate.

Blade continues to use Laravel's normal authorization API:

```blade
@can('order.create')
    ...
@endcan
```

For abilities involving a model:

```blade
@can('order.update', $order)
    ...
@endcan
```

Gate definitions may receive models or additional parameters.

## 11. Registration files may be colocated and discovered recursively

Registration files may live at a feature root or inside a specific use-case folder.

Supported conventions may include:

```text
gates.php
events.php
schedules.php
commands.php
web-routes.php
api-routes.php
```

Example:

```text
app/Features/
└── Order/
    ├── gates.php
    ├── schedules.php
    ├── Create/
    │   └── events.php
    └── Sync/
        └── schedules.php
```

Use a single `FeatureServiceProvider` as the bridge between Laravel and `app/Features`.

The provider may perform recursive discovery with Symfony Finder.

Example:

```php
use Symfony\Component\Finder\Finder;

private function loadFeatureFiles(string $filename): void
{
    $finder = Finder::create()
        ->files()
        ->name($filename)
        ->in(app_path('Features'));

    foreach ($finder as $file) {
        require $file->getRealPath();
    }
}
```

Do not create a Service Provider for every feature unless there is a concrete need for feature-specific bootstrapping.

## 12. Queue Jobs are adapters, not primary business logic

Queue Jobs do not require global registration.

They are dispatched normally:

```php
ImportOrdersJob::dispatch($payload);
```

A Job should remain thin and delegate to a use case:

```php
class ImportOrdersJob implements ShouldQueue
{
    public function handle(ImportOrders $action): void
    {
        $action->execute();
    }
}
```

Store the Job next to the use case it triggers:

```text
Order/
└── Import/
    ├── ImportOrders.php
    └── ImportOrdersJob.php
```

## 12. Use transactions around the atomic business change ---

Place related persistence operations inside a database transaction when they must succeed or fail together.

Dispatch cross-feature events only after the originating state is valid. When an event must not be processed before a transaction commits, use Laravel's after-commit behavior or another reliable post-commit mechanism.

Do not allow a queued listener to observe uncommitted or rolled-back state.

## 13. Treat queues and schedules as triggers

A queue job or scheduled task is not automatically a separate business use case. It is usually an adapter that invokes one:

```text
Scheduler → Job → ExpireOrders
CLI Command ───→ ExpireOrders
HTTP Controller → ExpireOrders
```

Use jobs for slow, retryable, or background work. Keep the job's `handle` method thin:

```php
final class ExpireOrdersJob implements ShouldQueue
{
    public function handle(ExpireOrders $expireOrders): void
    {
        $expireOrders->execute();
    }
}
```

Use safeguards such as `withoutOverlapping()` or `onOneServer()` when the scheduled operation requires them.

## 14. CLI Commands are adapters

CLI Commands should remain thin.

Example:

```php
class ExpireOrdersCommand extends Command
{
    public function handle(ExpireOrders $action): int
    {
        $action->execute();

        return self::SUCCESS;
    }
}
```

Store the Command next to its use case:

```text
Order/
└── Expire/
    ├── ExpireOrders.php
    └── ExpireOrdersCommand.php
```

Commands may be registered by `FeatureServiceProvider` through `$this->commands(...)` or through a consistent discovery convention.

## 15. Webhooks are HTTP adapters

A Webhook is still an HTTP entry point.

Store its controller next to the use case it triggers.

Example:

```text
Order/
└── SyncMarketplace/
    ├── WebhookController.php
    ├── Request.php
    └── SyncMarketplace.php
```

Webhook routes may be registered through `api-routes.php` or another colocated route file discovered by `FeatureServiceProvider`.

## 16. Internal processes usually require no explicit registration

Internal actions, calculators, rules, mappers, processors, and similar classes normally require no registration.

Use Laravel's container and autowiring directly.

Example:

```php
class SomeJob
{
    public function handle(ApproveOrder $action): void
    {
        $action->execute();
    }
}
```

## 17. Use Laravel Events for communication between top-level Features

**A top-level Feature must not directly depend on another Feature's internal implementation to trigger cross-feature side effects or workflows.**

Avoid:

```php
namespace App\Features\Order\Create;

use App\Features\Invoice\Create\CreateInvoice;

class CreateOrder
{
    public function __construct(
        private CreateInvoice $createInvoice,
    ) {}

    public function execute()
    {
        $order = ...;

        $this->createInvoice->execute($order);
    }
}
```

This creates direct coupling:

```text
Order
  ↓
Invoice
```

Instead, publish a Laravel Event:

```php
OrderCreated::dispatch($order);
```

A consuming Feature may listen for that event:

```php
namespace App\Features\Invoice\CreateFromOrder;

class OrderCreatedListener
{
    public function handle(
        OrderCreated $event,
        CreateInvoiceFromOrder $action,
    ): void {
        $action->execute($event->order);
    }
}
```

The relationship becomes:

```text
Order
  ↓
OrderCreated event
  ↑
Invoice listener
```

The producer must not need to know which Features consume the event.

## 18. Events represent facts that already happened

Event names should use domain language and normally describe a completed fact.

Prefer:

```text
OrderCreated
OrderApproved
OrderCancelled
PaymentReceived
InvoiceIssued
CustomerActivated
```

Avoid command-like event names:

```text
CreateInvoice
ProcessPayment
SendEmail
UpdateStock
```

A command describes something that should happen.

An event describes something that has already happened.

## 19. Event Listeners belong to the consuming Feature

A Listener belongs to the Feature that reacts to the event, not to the Feature that produced it.

Example:

```text
Order/
└── Create/
    └── CreateOrder.php

Invoice/
└── CreateFromOrder/
    ├── OrderCreatedListener.php
    └── CreateInvoiceFromOrder.php
```

The Event itself should live with the Feature that owns the fact being emitted.

For example:

```text
Order/
└── OrderCreated.php
```

or:

```text
Order/
└── Create/
    └── OrderCreated.php
```

Choose one convention and use it consistently throughout the project.

## 20. Avoid direct cross-feature implementation dependencies

By default, code inside one top-level Feature must not import internal implementation classes from another top-level Feature.

Avoid imports such as:

```php
use App\Features\Invoice\Create\CreateInvoice;
use App\Features\Customer\Update\UpdateCustomer;
```

from inside `App\Features\Order\...`.

If one Feature needs to react to a fact from another Feature, use an Event.

If multiple Features truly require the same reusable business logic, extract that logic into an appropriate shared abstraction or shared business concept instead of making Features call one another directly.

## 21. Event Listeners may be synchronous or queued

Use a synchronous Listener when:

- the operation is lightweight;
- it must complete in the same execution flow;
- immediate consistency is required.

Use a queued Listener when:

- the operation is expensive;
- it calls an external API;
- it sends email or notifications;
- it generates files or reports;
- it performs synchronization;
- it does not need to complete before returning the original response.

Example:

```php
class CreateInvoiceOnOrderCreated implements ShouldQueue
{
    public function handle(OrderCreated $event): void
    {
        // ...
    }
}
```

Choosing synchronous versus queued execution does not change the Feature boundary.

## 22. Do not use Events for every internal call

Events are primarily for **cross-feature communication**, decoupled reactions, and meaningful side effects.

Inside the same Feature, direct dependencies are allowed and usually clearer.

Example:

```text
Order/Create/CreateOrder
      ↓
Order/CalculatePricing
      ↓
Order/CheckCreditLimit
```

Do not replace clear internal control flow with unnecessary Events.

Use direct calls for required, explicit steps inside the same Feature.

Use Events when decoupling Feature boundaries or representing reactions to completed business facts.

## 23. Event registration follows the Feature convention

Listeners may be registered through Laravel's event discovery or through colocated `events.php` files.

Example:

```php
use Illuminate\Support\Facades\Event;

Event::listen(
    \App\Features\Order\OrderCreated::class,
    \App\Features\Invoice\CreateFromOrder\OrderCreatedListener::class,
);
```

The registration file may live next to the consuming use case:

```text
Invoice/
└── CreateFromOrder/
    ├── OrderCreatedListener.php
    ├── CreateInvoiceFromOrder.php
    └── events.php
```

`FeatureServiceProvider` may discover all `events.php` files recursively.

## 24. Avoid duplicated business logic

Do not copy business logic from one vertical slice to another merely to preserve slice isolation.

If logic becomes shared by multiple use cases within the same Feature, promote it to the nearest common parent.

Example:

```text
Order/
├── Create/
├── Update/
└── CalculatePricing.php
```

## 25. Do not introduce abstractions before they are needed

Do not create interfaces, repositories, managers, factories, or additional layers solely to satisfy an architectural pattern.

Start with the simplest implementation that clearly expresses the use case.

Extract an abstraction only when there is a concrete need, such as:

- multiple implementations;
- reusable business logic;
- a real testing boundary;
- an integration boundary;
- complexity that has already emerged.

## 26. Keep Laravel conventions where they remain useful

Feature-oriented organization does not mean replacing Laravel's native mechanisms.

Continue using Laravel features such as:

- Form Requests;
- Gates;
- Events;
- Listeners;
- Queues;
- Scheduler;
- Eloquent;
- Middleware;
- Service Container;
- Route Model Binding;
- Validation;
- Notifications.

The main architectural difference is **file ownership and locality**, not replacing Laravel's framework primitives.

## 27. Use one FeatureServiceProvider as the integration bridge

Use one main provider, for example:

```text
app/Providers/FeatureServiceProvider.php
```

It acts as the integration bridge:

```text
Laravel
   ↓
FeatureServiceProvider
   ↓
app/Features/**
```

The provider may discover and register:

```text
web-routes.php
api-routes.php
gates.php
events.php
schedules.php
commands.php
```

Do not place business logic inside `FeatureServiceProvider`.

## 28. Follow a clear dependency direction

Preferred dependency direction:

```text
Entry Point / Adapter
        ↓
     Use Case
        ↓
Feature-local shared component
        ↓
Framework / Model / Infrastructure
```

For cross-feature communication:

```text
Feature A
   ↓
 Event
   ↑
Feature B Listener
```

Avoid:

```text
Feature A
   ↓
Feature B internal implementation
```

## 29. Rules for AI-generated changes

Before creating or moving code, AI must determine:

1. Which domain owns the behavior?
2. Which use case performs it?
3. Is the file business logic, a shared domain component, or a thin adapter?
4. Is the component used by one slice, multiple slices in one domain, or multiple domains?
5. Does cross-feature communication require a business event?
6. Does Laravel require explicit registration or discovery for the new file?
7. Could ordering, transaction timing, queue retries, or idempotency affect correctness?

AI must preserve existing project conventions when they are more specific than this document. If a requested change conflicts with these rules, explain the conflict and choose the smallest safe deviation unless the user explicitly directs otherwise.

## 20. Reference structure

```text
app/Features/
├── FeatureServiceProvider.php
├── Order/
│   ├── Events/
│   │   └── OrderCreated.php
│   ├── Create/
│   │   ├── WebController.php
│   │   ├── ApiController.php
│   │   ├── WebhookController.php
│   │   ├── Request.php
│   │   ├── CreateOrder.php // main business logic
│   │   ├── Policy.php
│   │   ├── view.blade.php
│   │   ├── script.js // custom JS in page
│   │   └── style.css // custom styling
│   ├── Approve/
│   │   └── ApproveOrder.php
│   ├── Expire/
│   │   ├── ExpireOrders.php
│   │   ├── ExpireOrdersJob.php
│   │   └── schedules.php
│   ├── CalculatePricing.php
│   ├── CheckCreditLimit.php
│   ├── web-routes.php
│   ├── api-routes.php
│   └── gates.php
│
├── Invoice/
│   └── CreateFromOrder/
│       ├── Listener.php
│       ├── CreateInvoice.php
│       └── events.php
│
└── Notification/
    └── SendOrderConfirmation/
        ├── Listener.php
        ├── SendOrderConfirmation.php
        └── events.php
```

## 30. Core principles

Use these principles as the default architectural rules:

> **Code that changes together should live together.**

> **Organize code by business capability and use case, not by technical layer.**

> **Keep use-case-specific code local. Promote shared code only when reuse is real.**

> **Use direct calls inside a Feature; use Laravel Events for communication between top-level Features.**

> **Treat HTTP, CLI, Queue, Schedule, Webhook, and Event Listener as adapters into a use case, not as the architecture itself.**
