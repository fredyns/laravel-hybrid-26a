# 4. CRUD part 3 — REST API (single-action controllers)

API convention in this repo: **one invokable controller per action**, FormRequests for validation,
permission enforcement via `can:` **route middleware** (not in the controller), and Eloquent API
Resources for output. Reference implementation: `app/Http/Controllers/Api/Users/*`.

## 4.1 API Resource

`app/Http/Resources/TaskResource.php`:

```php
<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Task
 */
class TaskResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'notes' => $this->notes,
            'done' => $this->done,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```

## 4.2 FormRequests

`app/Http/Requests/Api/Tasks/StoreTaskRequest.php` (and `UpdateTaskRequest.php` — identical rules
for this resource, so you can make Update extend Store):

```php
<?php

namespace App\Http\Requests\Api\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // authorization happens in the route middleware (can:tasks.*)
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'done' => ['boolean'],
        ];
    }
}
```

```php
<?php

namespace App\Http\Requests\Api\Tasks;

class UpdateTaskRequest extends StoreTaskRequest
{
}
```

## 4.3 Controllers — one class per action

`app/Http/Controllers/Api/Tasks/IndexController.php` (search/sort/pagination mirror the Livewire
Index; the paginated Resource collection yields the standard `{data, links, meta}` envelope the SPA
expects):

```php
<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class IndexController extends Controller
{
    /**
     * List tasks with optional search, sorting and pagination.
     */
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', Rule::in(['title', 'done', 'created_at', 'updated_at'])],
            'sort_dir' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $tasks = Task::query()
            ->when(($validated['search'] ?? '') !== '', fn ($query) => $query->search($validated['search']))
            ->orderBy($validated['sort_by'] ?? 'created_at', $validated['sort_dir'] ?? 'desc')
            ->paginate($validated['per_page'] ?? 10);

        return TaskResource::collection($tasks);
    }
}
```

`ShowController.php`:

```php
<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;

class ShowController extends Controller
{
    public function __invoke(Task $task): TaskResource
    {
        return TaskResource::make($task);
    }
}
```

`StoreController.php`:

```php
<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tasks\StoreTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function __invoke(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());

        return response()->json([
            'message' => 'Task created',
            'task' => TaskResource::make($task),
        ], 201);
    }
}
```

`UpdateController.php`:

```php
<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tasks\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class UpdateController extends Controller
{
    public function __invoke(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());

        return response()->json([
            'message' => 'Task updated',
            'task' => TaskResource::make($task),
        ]);
    }
}
```

`DestroyController.php`:

```php
<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class DestroyController extends Controller
{
    public function __invoke(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'message' => 'Task deleted',
        ]);
    }
}
```

## 4.4 Routes

In `routes/api.php`, inside the existing `auth:sanctum` group (alias the controllers if names
collide with other features):

```php
use App\Http\Controllers\Api\Tasks\DestroyController as TaskDestroyController;
use App\Http\Controllers\Api\Tasks\IndexController as TaskIndexController;
use App\Http\Controllers\Api\Tasks\ShowController as TaskShowController;
use App\Http\Controllers\Api\Tasks\StoreController as TaskStoreController;
use App\Http\Controllers\Api\Tasks\UpdateController as TaskUpdateController;

Route::prefix('tasks')->group(function (): void {
    Route::get('/', TaskIndexController::class)
        ->middleware('can:tasks.index')
        ->name('api.tasks.index');

    Route::post('/', TaskStoreController::class)
        ->middleware('can:tasks.create')
        ->name('api.tasks.store');

    Route::get('{task}', TaskShowController::class)
        ->middleware('can:tasks.show')
        ->name('api.tasks.show');

    Route::put('{task}', TaskUpdateController::class)
        ->middleware('can:tasks.update')
        ->name('api.tasks.update');

    Route::delete('{task}', TaskDestroyController::class)
        ->middleware('can:tasks.delete')
        ->name('api.tasks.destroy');
});
```

Scramble picks these up automatically — the new endpoints appear at
http://localhost:9700/docs/api with schemas derived from the FormRequests and Resource.

## 4.5 Pest tests

`tests/Feature/Api/TasksApiTest.php` — cover the three layers every endpoint has: authentication
(401), authorization (403), and behavior/validation:

```php
<?php

use App\Models\Task;

test('guests receive 401 for task endpoints', function () {
    $task = Task::factory()->create();

    $this->getJson(route('api.tasks.index'))->assertUnauthorized();
    $this->postJson(route('api.tasks.store'))->assertUnauthorized();
    $this->getJson(route('api.tasks.show', $task))->assertUnauthorized();
});

test('users without permission receive 403', function () {
    $task = Task::factory()->create();

    $this->actingAs(apiPlainUser(), 'sanctum');

    $this->getJson(route('api.tasks.index'))->assertForbidden();
    $this->postJson(route('api.tasks.store'))->assertForbidden();
    $this->putJson(route('api.tasks.update', $task))->assertForbidden();
    $this->deleteJson(route('api.tasks.destroy', $task))->assertForbidden();
});

test('index returns paginated tasks and supports search', function () {
    Task::factory()->create(['title' => 'Findable task']);
    Task::factory()->create(['title' => 'Another one']);

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->getJson(route('api.tasks.index'))
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'title', 'done']], 'meta' => ['total']])
        ->assertJsonPath('meta.total', Task::count());

    $this->getJson(route('api.tasks.index', ['search' => 'Findable']))
        ->assertOk()
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.title', 'Findable task');
});

test('store creates a task', function () {
    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->postJson(route('api.tasks.store'), [
        'title' => 'Ship the API',
        'notes' => 'With tests.',
    ])
        ->assertCreated()
        ->assertJsonPath('task.title', 'Ship the API');

    expect(Task::where('title', 'Ship the API')->exists())->toBeTrue();
});

test('store validates input', function () {
    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->postJson(route('api.tasks.store'), ['title' => ''])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
});

test('update changes a task', function () {
    $task = Task::factory()->create();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->putJson(route('api.tasks.update', $task), [
        'title' => 'Renamed',
        'done' => true,
    ])->assertOk();

    $task->refresh();
    expect($task->title)->toBe('Renamed')
        ->and($task->done)->toBeTrue();
});

test('destroy deletes a task', function () {
    $task = Task::factory()->create();

    $this->actingAs(apiAdminUser(), 'sanctum');

    $this->deleteJson(route('api.tasks.destroy', $task))
        ->assertOk()
        ->assertJsonPath('message', 'Task deleted');

    expect(Task::find($task->id))->toBeNull();
});
```

Notes:
- `apiAdminUser()` / `apiPlainUser()` live in `tests/Pest.php` and set roles on **both guards**.
- Authenticate API tests with `$this->actingAs($user, 'sanctum')`.
- Model keys are uuid **objects** — when asserting an id in JSON, cast: `(string) $task->id`.

## Verify

```bash
docker exec livewire26d_app php artisan route:list --path=api/tasks
docker exec livewire26d_app php artisan test --compact --filter=TasksApi
docker exec livewire26d_app ./vendor/bin/pint --dirty
```

Manual smoke test with a real token:

```bash
# login → grab "token" from the response
curl -s -X POST http://localhost:9700/api/auth/login \
  -H 'Content-Type: application/json' -H 'Accept: application/json' \
  -d '{"email":"admin@admin.com","password":"admin"}'

curl -s http://localhost:9700/api/tasks -H 'Accept: application/json' -H 'Authorization: Bearer <token>'
```

Next: [part 4 — the React frontend →](05-crud-frontend.md)
