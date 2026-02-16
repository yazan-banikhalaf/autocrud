<?php

namespace Yazan\AutoCrud;

use Illuminate\Support\ServiceProvider;
use Yazan\AutoCrud\Commands\GenerateCrudCommand;

class AutoCrudServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateCrudCommand::class,
            ]);
        }
    }

    public function register()
    {
        //
    }
}
