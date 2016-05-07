<?php
namespace Sleimanx2\Plastic;

use Illuminate\Support\ServiceProvider;
use Sleimanx2\Plastic\Mappings\ElasticMappingRepository;

class MappingServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRepository();
    }


    protected function registerRepository()
    {
        $this->app->singleton('mapping.repository', function ($app) {

            $table = $app['config']['plastic.mappings'];

            return new ElasticMappingRepository($app['db'], $table);
        });
    }



}