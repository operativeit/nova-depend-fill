<?php

namespace EomPlus\Nova\Fields\DependFill;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Str;


class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::script('eom-depend-fill', __DIR__.'/../dist/js/field.js');
            Nova::style('eom-depend-fill', __DIR__.'/../dist/css/field.css');
        });
    }


    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
	/*
        if ($this->app->routesAreCached()) {
            return;
        }
        */

        Route::middleware('nova')
            ->prefix('nova-vendor/eom-depend-fill')
            ->namespace('EomPlus\Nova\Fields\DependFill\Http\Controllers')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
