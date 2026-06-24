<?php

return new class extends \App\DB\BaseInsertCollectionMigration {
    public string $fileName = 'users.php';
    public string $table = 'users';
    public string $uniqueColumn = 'email';
};
