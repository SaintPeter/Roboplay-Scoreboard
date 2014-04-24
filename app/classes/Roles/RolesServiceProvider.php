<?php

namespace Roles;

use Illuminate\Support\ServiceProvider;

class RolesServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register 'underlyingclass' instance container to our UnderlyingClass object
        $this->app['roles'] = $this->app->share(function($app)
        {
            return new Roles;
        });

        // Shortcut so developers don't need to add an Alias in app/config/app.php
        $this->app->booting(function()
        {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Roles', 'Roles\Facades\Roles');
        });
    }
}