<?php declare(strict_types=1);

namespace KiryaDev\Admin;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class AdminServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishers();
        }

        AdminCore::bootResourcesIn(app_path('Admin'));

        $this->app->singleton('admin.core', static function () {
            return new AdminCore;
        });

        $this->app->singleton('admin.asset', static function () {
            return new AdminAsset();
        });

        $this->registerAuthRoutes();

        $this->registerResourceRoutes();

        $this->registerViews();

        $this->useBootsrapInPaginator();
    }

    protected function registerPublishers(): void
    {
        $this->publishes([
            __DIR__ . '/../config/admin.php' => config_path('admin.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/admin'),
        ], 'assets');
    }

    protected function registerAuthRoutes(): void
    {
        Route::group($this->routeConfiguration('admin.auth.middleware'), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/auth.php');
        });
    }

    protected function registerResourceRoutes(): void
    {
        Route::group($this->routeConfiguration('admin.middleware'), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/resource.php');
        });
    }

    protected function routeConfiguration(string $middlewareConfigKey): array
    {
        return [
            'prefix' => config('admin.prefix'),
            'namespace' => 'KiryaDev\Admin\Http\Controllers',
            'middleware' => config($middlewareConfigKey),
            'as' => 'admin.',
        ];
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'admin');
    }

    protected function useBootsrapInPaginator(): void
    {
        Paginator::useBootstrap();
    }
}
