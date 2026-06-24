<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;

/**
 * todo: this controller shall reading from PHP enum object instead of json files
 */
class EnumsController
{
    /**
     * Get all available enums
     */
    public function __invoke(): JsonResponse
    {
        $enumsPath = public_path('enums');
        $enums = [];

        if (File::exists($enumsPath)) {
            $enums = $this->loadEnumsRecursively($enumsPath);
        }

        return response()->json([
            'data' => $enums,
        ]);
    }

    /**
     * Recursively load all enum JSON files
     */
    private function loadEnumsRecursively(string $path, string $prefix = ''): array
    {
        $enums = [];
        $files = File::files($path);
        $directories = File::directories($path);

        // Load JSON files
        foreach ($files as $file) {
            if ($file->getExtension() === 'json') {
                $name = $file->getFilenameWithoutExtension();
                $key = $prefix ? "{$prefix}.{$name}" : $name;
                $enums[$key] = json_decode($file->getContents(), true);
            }
        }

        // Recursively load from subdirectories
        foreach ($directories as $dir) {
            $dirName = basename($dir);
            $newPrefix = $prefix ? "{$prefix}.{$dirName}" : $dirName;
            $subEnums = $this->loadEnumsRecursively($dir, $newPrefix);
            $enums = array_merge($enums, $subEnums);
        }

        return $enums;
    }
}
