<?php

namespace Sleimanx2\Plastic;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Sleimanx2\Plastic\Facades\Plastic;

/**
 * @codeCoverageIgnore
 */
class PlasticServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (function_exists('config_path')) {
            $publishPath = config_path('plastic.php');
        } else {
            $publishPath = base_path('config/plastic.php');
        }

        // Publish the configuration path
        $this->publishes([
            __DIR__.'/Resources/config.php' => $publishPath,
        ]);

        // Create the mapping folder
        $this->publishes([
            __DIR__.'/Resources/database' => database_path(),
        ], 'database');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerManager();

        $this->registerProviders();

        $this->registerAlias();
    }

    /**
     *  Register plastic's Manager and connection.
     */
    protected function registerManager()
    {
        $this->app->singleton('plastic', function ($app) {
            return new PlasticManager($app);
        });

        $this->app->singleton('plastic.connection', function ($app) {
            return $app['plastic']->connection();
        });
    }

    /**
     * Register the service providers.
     */
    protected function registerProviders()
    {
        // Register the index service provider.
        $this->app->register(IndexServiceProvider::class);

        // Register the mappings service provider.
        $this->app->register(MappingServiceProvider::class);
    }

    /**
     *  Register the Plastic alias.
     */
    protected function registerAlias()
    {
        if (class_exists('Illuminate\Foundation\AliasLoader')) {
            AliasLoader::getInstance()->alias('Plastic', Plastic::class);
        } elseif (!class_exists('Plastic')) {
            class_alias(Plastic::class, 'Plastic');
        }
    }
}
