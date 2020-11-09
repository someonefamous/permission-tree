<?php

namespace SomeoneFamous\PermissionTree;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class PermissionTreeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'sf_permissions');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            if (!class_exists('SetUpPermissionsTables')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/set_up_permissions_tables.php.stub' => database_path(
                        'migrations/' . date('Y_m_d_His', time()) . '_set_up_permissions_tables.php'
                    ),
                ], 'migrations');
            }

            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('sf_permissions.php'),
            ], 'config');
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sf_permissions');

        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    protected function routeConfiguration()
    {
        return [
            'prefix' => config('sf_permissions.route_prefix'),
            'middleware' => config('sf_permissions.middleware'),
        ];
    }
}
