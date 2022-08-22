<?php

namespace Enflow\Component\Brick;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class BrickServiceProvider extends IlluminateServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/brick.php' => config_path('brick.php'),
        ], 'config');

        $this->loadViewsFrom(__DIR__.'/../views', 'brick');

        $this->app->singleton(BrickManager::class);

        View::composer('*', function ($view) {
            $view->with('brickManager', app(BrickManager::class));
        });

        $this->registerRoutes();

        if (! class_exists('CreateBrickDevicesTable')) {
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__.'/../migrations/create_brick_devices_table.php.stub' => database_path("/migrations/{$timestamp}_create_brick_devices_table.php"),
            ], 'migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/brick.php', 'brick');

        $this->app->bind('brick.user', fn() => auth(config('brick.user_guard'))->check() ? auth(config('brick.user_guard'))->user() : null);
    }

    private function registerRoutes()
    {
        Route::group([
            'middleware' => ['web', 'throttle:120,1'],
        ], function ($router) {
            $router->get('brick', ['as' => 'brick.index', 'uses' => Http\Controllers\BrickController::class . '@index']);
            $router->post('brick/receiver', ['as' => 'brick.receiver', 'uses' => Http\Controllers\BrickController::class . '@receiver']);
        });
    }
}
