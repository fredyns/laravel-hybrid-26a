# 2. CRUD part 1 — Database: table, permissions, model

We build a **Task** resource: `title` (string), `notes` (nullable text), `done` (boolean).
Everything below runs inside the app container:

```bash
docker exec hybrid26a_app php artisan <command>
```

## 2.1 Table migration

```bash
docker exec hybrid26a_app php artisan make:migration create_tasks_table --no-interaction
```

Models in this repo use **uuid primary keys** (like `users` and `rbac_roles`). Edit the generated
file:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('notes')->nullable();
            $table->boolean('done')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
```

## 2.2 Permission migration

Permissions are **data created by migrations**, not config. Create an empty migration and replace
its content with a `BasePermissionMigration` subclass (pattern:
`database/migrations/2026_06_24_240401_add_users_permissions.php`):

```bash
docker exec hybrid26a_app php artisan make:migration add_tasks_permissions --no-interaction
```

```php
<?php

use App\DB\BasePermissionMigration;
use App\Enums\UserRole;

return new class extends BasePermissionMigration
{
    public function permissions(): array
    {
        return [
            // 'name'                    → granted to SUPER-ADMIN only
            // 'name' => [UserRole::X]   → granted to X and SUPER-ADMIN
            'tasks.index' => [UserRole::INTERNAL],
            'tasks.show' => [UserRole::INTERNAL],
            'tasks.create',
            'tasks.update',
            'tasks.delete',
        ];
    }
};
```

That's the whole file. The base class creates each permission **for both guards** (`web` for the
server-rendered pages, `sanctum` for the API) and syncs the role grants; `down()` reverses it. No controller or
seeder code ever creates permissions.

## 2.3 Model + factory

```bash
docker exec hybrid26a_app php artisan make:model Task --no-interaction
docker exec hybrid26a_app php artisan make:factory TaskFactory --model=Task --no-interaction
```

`app/Models/Task.php` — follow the `User`/`Role` conventions (uuid keys, `#[Fillable]` attribute,
`Searchable` trait):

```php
<?php

namespace App\Models;

use App\Models\Traits\Searchable;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $title
 * @property string|null $notes
 * @property bool $done
 */
#[Fillable(['title', 'notes', 'done'])]
class Task extends Model
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    use HasUuids;
    use Searchable;

    /** @var list<string> */
    protected array $searchableFields = [
        'title',
        'notes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'done' => 'boolean',
        ];
    }
}
```

The `Searchable` trait gives you `Task::query()->search($term)` across `$searchableFields` — the
same one-keyword search the Users and Roles pages use.

`database/factories/TaskFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'notes' => fake()->optional()->paragraph(),
            'done' => false,
        ];
    }

    public function done(): static
    {
        return $this->state(fn (): array => ['done' => true]);
    }
}
```

## 2.4 Migrate

```bash
docker exec hybrid26a_app php artisan migrate --no-interaction
```

## Verify

```bash
# Table exists and model works
docker exec hybrid26a_app php artisan tinker --execute 'echo App\Models\Task::count();'

# Permissions exist on BOTH guards and super-admin has them
docker exec hybrid26a_app php artisan tinker --execute 'echo App\Models\RBAC\Permission::where("name", "like", "tasks.%")->get(["name","guard_name"]);'
```

Expected: `0` tasks, and ten permission rows (five names × two guards).

Because tests run migrations against sqlite `:memory:`, the new permissions are automatically
available in every test — nothing extra to seed.

Next: [part 2 — the web pages →](03-crud-web.md)
