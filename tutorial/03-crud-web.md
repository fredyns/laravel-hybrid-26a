# 3. CRUD part 2 — Web pages (invokable controllers + Blade)

The server-rendered side of a resource is **one invokable controller per page** rendering a plain
Blade view with Flux UI components — the same MVC pattern the Users and Roles pages use. The
reference implementation is `app/Http/Controllers/Users/*` with views in `resources/views/users/*`;
Tasks below is the same pattern trimmed to the essentials.

> **Where did Livewire go?** The Users/Roles/Notifications pages were converted from full-page
> Livewire components to controllers + Blade (list state lives in query strings, forms submit with
> plain GET). Livewire still powers the **settings** pages (`routes/settings.php` →
> `app/Livewire/Pages/Settings/*`) and is available for interactive islands, but new list/detail
> pages follow the controller pattern shown here. Create/update/delete flows are handled by the
> REST API + React SPA ([part 3](04-crud-api.md) and [part 4](05-crud-frontend.md)); on the web
> side, mutation buttons are rendered `@can`-gated (and disabled until a web form flow exists —
> exactly like the current Users/Roles pages).

## 3.1 Index controller

`app/Http/Controllers/Tasks/TaskIndexController.php` — list state (search, sort, page size) comes
from the query string, so the page is bookmarkable and needs no server-side state:

```php
<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskIndexController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Request $request): View
    {
        $this->authorize('tasks.index');

        $search = $request->query('q', '');
        $sortColumn = $request->query('sort', 'created_at');
        $sortDirection = $request->query('direction', 'desc');
        $perPage = (int) $request->query('per_page', 10);

        $tasks = Task::query()
            ->when($search !== '', fn ($query) => $query->search($search))
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage)
            ->appends($request->query());

        return view('tasks.index', compact('tasks', 'search', 'sortColumn', 'sortDirection', 'perPage'));
    }
}
```

The recurring pattern: **authorize first**, read list state from `$request->query()`, `search()`
comes from the model's `Searchable` trait, and `appends()` keeps the query string on pagination
links.

## 3.2 Show controller

`app/Http/Controllers/Tasks/TaskShowController.php`:

```php
<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class TaskShowController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Task $task): View
    {
        $this->authorize('tasks.show');

        return view('tasks.show', compact('task'));
    }
}
```

Route-model binding resolves `{task}` by uuid automatically.

## 3.3 Blade views (Flux UI)

`resources/views/tasks/index.blade.php` — the filter bar is a plain `GET` form that resubmits on
change; sortable headers are links that flip the `sort`/`direction` query params:

```blade
<x-layouts::app :title="__('Tasks')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Tasks') }}</flux:heading>
            <flux:subheading>{{ __('Track things to do.') }}</flux:subheading>
        </div>

        @can('tasks.create')
            <span class="cursor-not-allowed opacity-50">
                <flux:button variant="primary" icon="plus">
                    {{ __('New task') }}
                </flux:button>
            </span>
        @endcan
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <flux:input
                class="max-w-xs"
                name="q"
                value="{{ $search }}"
                icon="magnifying-glass"
                :placeholder="__('Search tasks...')"
                onchange="this.form.submit()"
            />

            <flux:spacer />

            <select name="per_page" onchange="this.form.submit()" class="max-w-[7rem] rounded border border-gray-300 px-2 py-1 text-sm">
                <option value="10" {{ $perPage === 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $perPage === 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage === 50 ? 'selected' : '' }}>50</option>
            </select>
        </form>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>
                <a href="{{ request()->url() }}?{{ http_build_query(array_merge(request()->query(), ['sort' => 'title', 'direction' => $sortColumn === 'title' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline">
                    {{ __('Title') }}
                    @if ($sortColumn === 'title')
                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                    @endif
                </a>
            </flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>
                <a href="{{ request()->url() }}?{{ http_build_query(array_merge(request()->query(), ['sort' => 'created_at', 'direction' => $sortColumn === 'created_at' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline">
                    {{ __('Created') }}
                    @if ($sortColumn === 'created_at')
                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                    @endif
                </a>
            </flux:table.column>
            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($tasks as $task)
                <flux:table.row :key="$task->id">
                    <flux:table.cell variant="strong">
                        <a href="{{ route('tasks.show', $task) }}" class="hover:underline">{{ $task->title }}</a>
                    </flux:table.cell>

                    <flux:table.cell>
                        @if ($task->done)
                            <flux:badge color="green" size="sm">{{ __('Done') }}</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">{{ __('Open') }}</flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>{{ $task->created_at?->format('Y-m-d H:i') }}</flux:table.cell>

                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-1">
                            <flux:tooltip :content="__('View')">
                                <a href="{{ route('tasks.show', $task) }}">
                                    <flux:button size="sm" variant="ghost" icon="eye" :aria-label="__('View')" />
                                </a>
                            </flux:tooltip>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="4">
                        <div class="py-6 text-center">
                            <flux:text>{{ __('No tasks found.') }}</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{ $tasks->links() }}
    </div>
</x-layouts::app>
```

`resources/views/tasks/show.blade.php`:

```blade
<x-layouts::app :title="$task->title">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('tasks.index') }}">
            <flux:button variant="ghost" icon="arrow-left" size="sm" />
        </a>
        <div>
            <flux:heading size="xl">{{ $task->title }}</flux:heading>
            <flux:subheading>
                @if ($task->done)
                    <flux:badge color="green" size="sm">{{ __('Done') }}</flux:badge>
                @else
                    <flux:badge color="zinc" size="sm">{{ __('Open') }}</flux:badge>
                @endif
            </flux:subheading>
        </div>
    </div>

    <flux:separator />

    <div class="grid max-w-2xl grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <flux:text size="sm" class="text-zinc-500">{{ __('Notes') }}</flux:text>
            <flux:text class="font-medium">{{ $task->notes ?: '—' }}</flux:text>
        </div>

        <div>
            <flux:text size="sm" class="text-zinc-500">{{ __('Created') }}</flux:text>
            <flux:text class="font-medium">{{ $task->created_at?->format('Y-m-d H:i') }}</flux:text>
        </div>
    </div>
    </div>
</x-layouts::app>
```

## 3.4 Routes

In `routes/web.php`, inside the existing `auth` + `verified` group (permission checks happen in
the controllers via `$this->authorize()`, matching Users/Roles):

```php
use App\Http\Controllers\Tasks\TaskIndexController;
use App\Http\Controllers\Tasks\TaskShowController;

Route::get('tasks', TaskIndexController::class)->name('tasks.index');
Route::get('tasks/{task}', TaskShowController::class)->name('tasks.show');
```

## 3.5 Sidebar & top-bar navigation

The hybrid layout has several nav variants. Add a permission-gated item to each file that lists
the Users/Roles entries — `resources/views/layouts/app/switchable-side.blade.php`,
`switchable-top.blade.php`, `switchable.blade.php`, and `sidebar.blade.php`:

```blade
@can('tasks.index')
    <flux:sidebar.item icon="clipboard-document-list" :href="route('tasks.index')" :current="request()->routeIs('tasks.*')" wire:navigate>
        {{ __('Tasks') }}
    </flux:sidebar.item>
@endcan
```

(In the top-bar variants use `flux:navbar.item` instead of `flux:sidebar.item`.)

Optional: breadcrumbs. Extend the `$crumbs` mapping where the layouts build it (see
`switchable-side.blade.php` / `sidebar.blade.php`):

```php
} elseif (str_starts_with($route, 'tasks.')) {
    $crumbs[] = ['label' => __('Tasks'), 'href' => route('tasks.index')];
}
```

## 3.6 Feature test

`tests/Feature/TasksTest.php` (helpers `apiAdminUser()` / `apiPlainUser()` come from `tests/Pest.php`):

```php
<?php

use App\Models\Task;

test('tasks page is displayed for authorized users', function () {
    $this->actingAs(apiAdminUser());

    $this->get(route('tasks.index'))->assertOk();
});

test('tasks page is forbidden without permission', function () {
    $this->actingAs(apiPlainUser());

    $this->get(route('tasks.index'))->assertForbidden();
});

test('index can search tasks by title', function () {
    $this->actingAs(apiAdminUser());

    Task::factory()->create(['title' => 'Findable task']);
    Task::factory()->create(['title' => 'Another one']);

    $this->get(route('tasks.index', ['q' => 'Findable']))
        ->assertOk()
        ->assertSee('Findable task')
        ->assertDontSee('Another one');
});

test('show displays a task', function () {
    $this->actingAs(apiAdminUser());

    $task = Task::factory()->create(['notes' => 'Cover every layer.']);

    $this->get(route('tasks.show', $task))
        ->assertOk()
        ->assertSee($task->title)
        ->assertSee('Cover every layer.');
});
```

## Verify

```bash
docker exec hybrid26a_app php artisan route:list --path=tasks
docker exec hybrid26a_app php artisan test --compact --filter=TasksTest
docker exec hybrid26a_app ./vendor/bin/pint --dirty
```

Then log in at http://localhost:9900/login as `admin@admin.com` / `admin` → **Tasks** appears in
the sidebar → search, sort, paginate, and open a task. Log in as a user without the permission and
confirm the nav entry and page are gone (403).

Next: [part 3 — the REST API →](04-crud-api.md)
