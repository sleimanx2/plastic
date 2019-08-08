<?php

namespace Nuwber\Plastic;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Nuwber\Plastic\Console\Mapping\Install;
use Nuwber\Plastic\Console\Mapping\Make;
use Nuwber\Plastic\Console\Mapping\ReRun;
use Nuwber\Plastic\Console\Mapping\Reset;
use Nuwber\Plastic\Console\Mapping\Run;
use Nuwber\Plastic\Facades\Map;
use Nuwber\Plastic\Mappings\Creator;
use Nuwber\Plastic\Mappings\Mapper;
use Nuwber\Plastic\Mappings\Mappings;

/**
 * @codeCoverageIgnore
 */
class MappingServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerRepository();

        $this->registerMapper();

        $this->registerCreator();

        $this->registerCommands();

        $this->registerAlias();
    }

    /**
     * Register the mapping repository service.
     */
    protected function registerRepository()
    {
        $this->app->singleton('mapping.repository', function ($app) {
            $table = $app['config']['plastic.mappings'];

            return new Mappings($app['db'], $table);
        });
    }

    /**
     * Register the mapping creator service.
     */
    protected function registerCreator()
    {
        $this->app->singleton('mapping.creator', function ($app) {
            return new Creator($app['files']);
        });
    }

    /**
     * Register the mapper service.
     */
    protected function registerMapper()
    {
        $this->app->singleton('mapping.mapper', function ($app) {
            return new Mapper($app['mapping.repository'], $app['files']);
        });
    }

    /**
     * Register all needed commands.
     */
    protected function registerCommands()
    {
        $commands = ['Install', 'Reset', 'Make', 'Run', 'ReRun'];

        foreach ($commands as $command) {
            $this->{'register'.$command.'Command'}();
        }

        $this->commands([
            'command.mapping.install',
            'command.mapping.reset',
            'command.mapping.make',
            'command.mapping.run',
            'command.mapping.rerun',
        ]);
    }

    /**
     * Register the Install command.
     */
    protected function registerInstallCommand()
    {
        $this->app->singleton('command.mapping.install', function ($app) {
            return new Install($app['mapping.repository']);
        });
    }

    /**
     * Register the Install command.
     */
    protected function registerRunCommand()
    {
        $this->app->singleton('command.mapping.run', function ($app) {
            return new Run($app['mapping.mapper']);
        });
    }

    /**
     * Register the Install command.
     */
    protected function registerReRunCommand()
    {
        $this->app->singleton('command.mapping.rerun', function ($app) {
            return new ReRun();
        });
    }

    /**
     * Register the reset command.
     */
    protected function registerResetCommand()
    {
        $this->app->singleton('command.mapping.reset', function ($app) {
            return new Reset($app['mapping.repository']);
        });
    }

    /**
     * Register the make command.
     */
    protected function registerMakeCommand()
    {
        $this->app->singleton('command.mapping.make', function ($app) {
            return new Make($app['mapping.creator'], $app['composer']);
        });
    }

    /**
     *  Register the Map alias.
     */
    protected function registerAlias()
    {
        AliasLoader::getInstance()->alias('Map', Map::class);
    }
}
