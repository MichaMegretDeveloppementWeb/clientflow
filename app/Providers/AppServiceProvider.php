<?php

namespace App\Providers;

use App\Services\JsonLdService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Inertia::version(fn () => vite()->manifestHash());

        // Directive Blade pour JSON-LD
        Blade::directive('jsonld', function ($expression) {
            return "<?php echo JsonLdService::render($expression); ?>";
        });
    }
}
