<?php

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Symfony\Component\Finder\Finder;

/**
 * todo: move to app/features
 */

/**
 * The single bridge between Laravel and app/Features.
 *
 * Discovers colocated registration files recursively and registers the
 * `features::` view and Livewire component namespaces. Contracts for each
 * discovered file are documented in .ai/rules/Vertical-Slice-Architecture.md §29.
 */
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

        $this->registerRoutes();
        $this->requireAll('gates.php');
        $this->requireAll('events.php');
        $this->registerCommands();
        $this->registerSchedules();
    }

    private function registerRoutes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        foreach ($this->featureFiles('web-routes.php') as $path) {
            Route::middleware('web')->group($path);
        }

        foreach ($this->featureFiles('api-routes.php') as $path) {
            Route::middleware('api')->prefix('api')->group($path);
        }
    }

    private function registerCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $commands = [];

        foreach ($this->featureFiles('commands.php') as $path) {
            $commands = [...$commands, ...require $path];
        }

        $this->commands($commands);
    }

    private function registerSchedules(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->callAfterResolving(Schedule::class, function (): void {
            $this->requireAll('schedules.php');
        });
    }

    private function requireAll(string $filename): void
    {
        foreach ($this->featureFiles($filename) as $path) {
            require $path;
        }
    }

    /**
     * @return list<string>
     */
    private function featureFiles(string $filename): array
    {
        if (! is_dir(app_path('Features'))) {
            return [];
        }

        $paths = [];

        $finder = Finder::create()
            ->files()
            ->name($filename)
            ->in(app_path('Features'))
            ->sortByName();

        foreach ($finder as $file) {
            $paths[] = $file->getRealPath();
        }

        return $paths;
    }
}
