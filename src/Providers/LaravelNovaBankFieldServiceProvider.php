<?php

declare(strict_types = 1);

namespace Wame\LaravelNovaBankField\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class LaravelNovaBankFieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('bank', __DIR__ . '/../../dist/js/field.js');
            Nova::style('bank', __DIR__ . '/../../dist/css/field.css');
            Nova::translations(__DIR__ . '/../../resources/lang/' . app()->getLocale() . '.json');
        });

        $this->loadTranslationsFrom( __DIR__ . '/../../resources/lang', 'translations');
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
