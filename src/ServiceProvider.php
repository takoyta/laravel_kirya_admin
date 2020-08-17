<?php declare(strict_types=1);

namespace KiryaDev\Admin;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\Route;
use KiryaDev\Admin\Http\Middleware\AdminMiddleware;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishers();
        }

        Core::bootResourcesIn(app_path('Admin'));

        $this->app->singleton('admin', function() {
            return new Core;
        });

        $this->registerAuthRoutes();

        $this->registerResourceRoutes();

        $this->registerViews();
    }

    protected function registerPublishers()
    {
        $this->publishes([
            __DIR__.'/../config/admin.php' => config_path('admin.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/admin'),
        ], 'assets');
    }

    protected function registerResourceRoutes()
    {
        Route::group($this->routeConfiguration('admin.middleware'), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/resource.php');
        });
    }

    protected function routeConfiguration($middlewareConfigKey)
    {
        return [
            'prefix' => config('admin.prefix'),
            'namespace' => 'KiryaDev\Admin\Http\Controllers',
            'middleware' => config($middlewareConfigKey),
            'as' => 'admin.',
        ];
    }

    protected function registerAuthRoutes()
    {
        Route::group($this->routeConfiguration('admin.auth.middleware'), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/auth.php');
        });
    }

    protected function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'admin');
    }
}
