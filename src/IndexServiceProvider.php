<?php

namespace Sleimanx2\Plastic;

use Illuminate\Support\ServiceProvider;
use Sleimanx2\Plastic\Console\Index\Populate;

/**
 * @codeCoverageIgnore
 */
class IndexServiceProvider extends ServiceProvider
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
        $this->registerCommands();
    }

    /**
     * Register all needed commands.
     */
    protected function registerCommands()
    {
        $commands = ['Populate'];

        foreach ($commands as $command) {
            $this->{'register'.$command.'Command'}();
        }

        $this->commands([
            'command.index.populate',
        ]);
    }

    /**
     * Register the Install command.
     */
    protected function registerPopulateCommand()
    {
        $this->app->singleton('command.index.populate', function () {
            return new Populate();
        });
    }
}
