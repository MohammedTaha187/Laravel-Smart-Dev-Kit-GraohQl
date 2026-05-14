<?php

namespace Modules\Sales;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class SalesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bindings will be handled here or via discovery if possible
    }

    public function boot(): void
    {
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        if (file_exists(__DIR__ . '/Routes/api.php')) {
            Route::prefix('api/v1')
                ->middleware('api')
                ->group(__DIR__ . '/Routes/api.php');
        }
    }
}
