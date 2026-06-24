<?php

namespace App\DB;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Base class for permission migrations.
 *
 * Simplifies adding permissions and assigning them to roles.
 * Supports multiple guards (web, sanctum).
 */
abstract class BaseInsertCollectionMigration extends Migration
{
    public string $fileName = '';

    public string $table = '';

    public string $uniqueColumn = '';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $data = (array)include database_path('collections/' . $this->fileName);
        DB::table($this->table)->insert($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $data = (array)include database_path('collections/' . $this->fileName);
        DB::table($this->table)->whereIn($this->uniqueColumn, array_column($data, $this->uniqueColumn))->delete();
    }
}
