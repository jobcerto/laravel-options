<?php

namespace Jobcerto\Options;

use Illuminate\Support\ServiceProvider;

class OptionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        }

        if ( ! class_exists('CreateOptionsTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../database/migrations/create_options_table.php.stub' => database_path('migrations/' . $timestamp . '_create_options_table.php'),
            ], 'migrations');
        }

        $this->publishes([
            __DIR__ . '/../config/options.php' => config_path('options.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/options.php', 'options');
    }
}
