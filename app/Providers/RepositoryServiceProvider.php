<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

/**
 * RepositoryServiceProvider
 *
 * Auto-binds Service and Repository interfaces to their implementations.
 * Pattern: app/Services/User/UserService.php => app/Services/User/Contracts/UserServiceInterface.php
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Manual bindings if needed.
     */
    public array $bindings = [];

    public function register(): void
    {
        // 1. Discover App-level bindings
        $this->autoDiscover(app_path('Services'), 'App\\Services');
        $this->autoDiscover(app_path('Repositories'), 'App\\Repositories');

        // 2. Discover Module-level bindings
        $this->discoverModules();
    }

    protected function autoDiscover(string $basePath, string $baseNs): void
    {
        if (! File::exists($basePath)) {
            return;
        }

        foreach (File::allFiles($basePath) as $file) {
            // Skip files inside 'Contracts' directories themselves
            if (str_contains($file->getRelativePathname(), 'Contracts')) {
                continue;
            }

            $relativePath = str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());
            
            // Implementation: App\Services\User\UserService
            $implClass = $baseNs . '\\' . $relativePath;

            // Interface calculation
            $parts = explode('\\', $relativePath);
            $className = array_pop($parts);
            $subNs = ! empty($parts) ? implode('\\', $parts) . '\\' : '';
            
            // Expected Interface: App\Services\User\Contracts\UserServiceInterface
            $interface = $baseNs . '\\' . $subNs . 'Contracts\\' . $className . 'Interface';

            if (class_exists($implClass) && interface_exists($interface)) {
                $this->app->bind($interface, $implClass);
            }
        }
    }

    protected function discoverModules(): void
    {
        $modulesPath = base_path('Modules');
        if (! File::exists($modulesPath)) return;

        foreach (File::directories($modulesPath) as $moduleDir) {
            $moduleName = basename($moduleDir);
            $this->autoDiscover("{$moduleDir}/Services", "Modules\\{$moduleName}\\Services");
            $this->autoDiscover("{$moduleDir}/Repositories", "Modules\\{$moduleName}\\Repositories");
        }
    }
}
