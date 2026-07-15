# 3. CRUD part 2 — Livewire page (fullstack side)

The Livewire side of a resource is two class-based components — an **Index** page and a **Form**
modal — plus blades, a route, and a nav entry. The reference implementation is
`app/Livewire/Pages/Users/*` with views in `resources/views/livewire/pages/users/*`; Tasks below is
the same pattern trimmed to the essentials.

## 3.1 Index component

`app/Livewire/Pages/Tasks/Index.php`:

```php
<?php

namespace App\Livewire\Pages\Tasks;

use App\Models\Task;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Tasks')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public int $perPage = 10;

    public string $sortColumn = 'created_at';

    public string $sortDirection = 'desc';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortColumn = $column;
        $this->sortDirection = 'asc';
    }

    public function create(): void
    {
        $this->authorize('tasks.create');

        $this->dispatch('task-create');
    }

    public function edit(string $taskId): void
    {
        $this->authorize('tasks.update');

        $this->dispatch('task-edit', taskId: $taskId);
    }

    public function toggleDone(string $taskId): void
    {
        $this->authorize('tasks.update');

        $task = Task::query()->findOrFail($taskId);
        $task->update(['done' => ! $task->done]);
    }

    public function delete(string $taskId): void
    {
        $this->authorize('tasks.delete');

        Task::query()->findOrFail($taskId)->delete();

        Flux::toast(variant: 'success', text: __('Task deleted.'));
    }

    #[On('tasks-updated')]
    public function refreshList(): void
    {
        // Triggered by the form modal after create/update so the list re-renders.
    }

    #[Computed]
    public function tasks(): LengthAwarePaginator
    {
        return Task::query()
            ->when($this->search !== '', fn ($query) => $query->search($this->search))
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.pages.tasks.index');
    }
}
```

The recurring pattern: **every action authorizes first**, list state lives in `#[Url]`/public
props, the query is a `#[Computed]`, and cross-component communication is `dispatch()` + `#[On]`.

## 3.2 Form modal component

`app/Livewire/Pages/Tasks/Form.php`:

```php
<?php

namespace App\Livewire\Pages\Tasks;

use App\Models\Task;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;

class Form extends Component
{
    public ?string $taskId = null;

    public string $title = '';

    public string $notes = '';

    public bool $done = false;

    public function isEditing(): bool
    {
        return $this->taskId !== null;
    }

    #[On('task-create')]
    public function createTask(): void
    {
        $this->authorize('tasks.create');

        $this->resetForm();

        $this->modal('task-form')->show();
    }

    #[On('task-edit')]
    public function editTask(string $taskId): void
    {
        $this->authorize('tasks.update');

        $task = Task::query()->findOrFail($taskId);

        $this->resetForm();
        $this->taskId = $task->id;
        $this->title = $task->title;
        $this->notes = $task->notes ?? '';
        $this->done = $task->done;

        $this->modal('task-form')->show();
    }

    public function save(): void
    {
        $this->authorize($this->isEditing() ? 'tasks.update' : 'tasks.create');

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'done' => ['boolean'],
        ]);

        if ($this->isEditing()) {
            Task::query()->findOrFail($this->taskId)->update($validated);
        } else {
            Task::create($validated);
        }

        Flux::toast(variant: 'success', text: $this->isEditing() ? __('Task updated.') : __('Task created.'));

        $this->dispatch('tasks-updated');

        $this->modal('task-form')->close();

        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset(['taskId', 'title', 'notes', 'done']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.pages.tasks.form');
    }
}
```

## 3.3 Blade views (Flux UI)

`resources/views/livewire/pages/tasks/index.blade.php`:

```blade
<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Tasks') }}</flux:heading>
            <flux:subheading>{{ __('Track things to do.') }}</flux:subheading>
        </div>

        @can('tasks.create')
            <flux:button variant="primary" icon="plus" wire:click="create">
                {{ __('New task') }}
            </flux:button>
        @endcan
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <flux:input
            class="max-w-xs"
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            :placeholder="__('Search tasks...')"
            clearable
        />

        <flux:spacer />

        <flux:select class="max-w-[7rem]" wire:model.live="perPage">
            <flux:select.option value="10">10</flux:select.option>
            <flux:select.option value="25">25</flux:select.option>
            <flux:select.option value="50">50</flux:select.option>
        </flux:select>
    </div>

    <flux:table :paginate="$this->tasks">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortColumn === 'title'" :direction="$sortDirection" wire:click="sort('title')">
                {{ __('Title') }}
            </flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column sortable :sorted="$sortColumn === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">
                {{ __('Created') }}
            </flux:table.column>
            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->tasks as $task)
                <flux:table.row :key="$task->id">
                    <flux:table.cell variant="strong">{{ $task->title }}</flux:table.cell>

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
                            @can('tasks.update')
                                <flux:tooltip :content="__('Toggle done')">
                                    <flux:button size="sm" variant="ghost" icon="check" wire:click="toggleDone('{{ $task->id }}')" :aria-label="__('Toggle done')" />
                                </flux:tooltip>

                                <flux:tooltip :content="__('Edit')">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="edit('{{ $task->id }}')" :aria-label="__('Edit')" />
                                </flux:tooltip>
                            @endcan

                            @can('tasks.delete')
                                <flux:tooltip :content="__('Delete')">
                                    <flux:button
                                        size="sm"
                                        variant="ghost"
                                        icon="trash"
                                        wire:click="delete('{{ $task->id }}')"
                                        wire:confirm="{{ __('Delete this task?') }}"
                                        :aria-label="__('Delete')"
                                    />
                                </flux:tooltip>
                            @endcan
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

    <livewire:pages.tasks.form />
</div>
```

`resources/views/livewire/pages/tasks/form.blade.php`:

```blade
<flux:modal name="task-form" class="w-full max-w-lg">
    <form wire:submit="save" class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $this->isEditing() ? __('Edit task') : __('Create task') }}</flux:heading>
            <flux:subheading>
                {{ $this->isEditing() ? __('Update this task.') : __('Add a new task.') }}
            </flux:subheading>
        </div>

        <flux:input wire:model="title" :label="__('Title')" type="text" required autofocus />

        <flux:textarea wire:model="notes" :label="__('Notes')" rows="4" />

        <flux:checkbox wire:model="done" :label="__('Done')" />

        <div class="flex justify-end gap-2">
            <flux:modal.close>
                <flux:button variant="filled" type="button">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" type="submit">
                {{ $this->isEditing() ? __('Save changes') : __('Create task') }}
            </flux:button>
        </div>
    </form>
</flux:modal>
```

## 3.4 Route

In `routes/web.php`, inside the existing `auth` + `verified` group:

```php
use App\Livewire\Pages\Tasks\Index as TasksIndex;

Route::livewire('tasks', TasksIndex::class)
    ->middleware('can:tasks.index')
    ->name('tasks.index');
```

## 3.5 Sidebar & top-bar navigation

The hybrid layout has two nav files. Add a link to each, permission-gated:

`resources/views/layouts/app/switchable-side.blade.php` — inside `<flux:sidebar.nav>` after the
Dashboard item (and again in the mobile sidebar section of `switchable-top.blade.php`):

```blade
@can('tasks.index')
    <flux:sidebar.item icon="clipboard-document-list" :href="route('tasks.index')" :current="request()->routeIs('tasks.*')" wire:navigate>
        {{ __('Tasks') }}
    </flux:sidebar.item>
@endcan
```

`resources/views/layouts/app/switchable-top.blade.php` — inside `<flux:navbar>`:

```blade
@can('tasks.index')
    <flux:navbar.item icon="clipboard-document-list" :href="route('tasks.index')" :current="request()->routeIs('tasks.*')" wire:navigate>
        {{ __('Tasks') }}
    </flux:navbar.item>
@endcan
```

Optional: breadcrumbs. In `resources/views/layouts/app.blade.php`, extend the `$crumbs` mapping:

```php
} elseif (str_starts_with($route, 'tasks.')) {
    $crumbs[] = ['label' => __('Tasks'), 'href' => route('tasks.index')];
}
```

## 3.6 Feature test

`tests/Feature/TasksTest.php` (helpers `apiAdminUser()` / `apiPlainUser()` come from `tests/Pest.php`):

```php
<?php

use App\Livewire\Pages\Tasks\Form;
use App\Models\Task;
use Livewire\Livewire;

test('tasks page is displayed for authorized users', function () {
    $this->actingAs(apiAdminUser());

    $this->get(route('tasks.index'))->assertOk();
});

test('tasks page is forbidden without permission', function () {
    $this->actingAs(apiPlainUser());

    $this->get(route('tasks.index'))->assertForbidden();
});

test('a task can be created from the form modal', function () {
    $this->actingAs(apiAdminUser());

    Livewire::test(Form::class)
        ->call('createTask')
        ->set('title', 'Write the tutorial')
        ->set('notes', 'Cover every layer.')
        ->call('save')
        ->assertHasNoErrors();

    expect(Task::where('title', 'Write the tutorial')->exists())->toBeTrue();
});

test('the form validates required fields', function () {
    $this->actingAs(apiAdminUser());

    Livewire::test(Form::class)
        ->call('createTask')
        ->set('title', '')
        ->call('save')
        ->assertHasErrors(['title' => 'required']);
});
```

## Verify

```bash
docker exec livewire26d_app php artisan route:list --path=tasks
docker exec livewire26d_app php artisan test --compact --filter=TasksTest
docker exec livewire26d_app ./vendor/bin/pint --dirty
```

Then log in at http://localhost:9700/login as `admin@admin.com` / `admin` → **Tasks** appears in
the sidebar → create, edit, toggle and delete a task. Log in as a user without the permission and
confirm the nav entry and page are gone (403).

Next: [part 3 — the REST API →](04-crud-api.md)
