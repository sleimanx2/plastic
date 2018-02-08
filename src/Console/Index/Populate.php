<?php

namespace Sleimanx2\Plastic\Console\Index;

use Elasticsearch\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Sleimanx2\Plastic\Facades\Plastic;

class Populate extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'plastic:populate 
                            {--mappings : Create the models mappings before populating the index}
                            {--database= : Database connection to use instead of the default one }
                            {--index= : Index to populate instead of the default one}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates an index';

    /**
     * Gets the client.
     *
     * @return Client
     */
    public function client()
    {
        return Plastic::getClient();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $index = $this->index();

        // Checks if the target index exists
        if (!$this->existsStatement($index)) {
            $this->error('Index « '.$index.' » does not exists.');

            return;
        }

        // Runs the mappings
        if ($this->option('mappings')) {
            $this->call('mapping:rerun', [
                '--index'    => $index,
                '--database' => $this->option('database'),
                '--force'    => true,
            ]);
        }

        // Populates the index
        try {
            $this->populateIndex($index);
        } catch (\Exception $e) {
            $this->warn('An error occured while populating the new index !');

            throw $e;
        }
    }

    /**
     * Populates the index.
     *
     * @param string $index The index name
     *
     * @throws \Exception
     */
    protected function populateIndex($index)
    {
        $this->line('Populating the index « '.$index.' » ...');

        // Replaces the current default index by the one we want to populate
        $defaultIndex = Plastic::getDefaultIndex();
        Plastic::setDefaultIndex($index);

        // Disables query logging to prevent memory leak
        $logging = DB::connection()->logging();
        DB::connection()->disableQueryLog();

        // Populates from models
        $models = $this->models($index);
        $chunkSize = $this->chunkSize();
        foreach ($models as $model) {
            $this->line('Indexing documents of model « '.$model.' » ...');
            $model::chunk($chunkSize, function ($items) {
                $this->line('Indexing chunk of '.$items->count().' documents ...');
                Plastic::persist()->bulkSave($items);
            });
        }

        // Restores query logging
        if ($logging) {
            DB::connection()->enableQueryLog();
        }

        // Restores the current default index
        Plastic::setDefaultIndex($defaultIndex);
    }

    /**
     * Gets the index to populate.
     *
     * @return array|string
     */
    protected function index()
    {
        return $this->option('index') ?? Plastic::getDefaultIndex();
    }

    /**
     * Execute a exists statement for index.
     *
     * @param $index
     *
     * @return bool
     */
    protected function existsStatement($index)
    {
        return $this->client()->indices()->exists(['index' => $index]);
    }

    /**
     * Gets the models to index for the given index.
     *
     * @param $index
     *
     * @return array
     */
    protected function models($index)
    {
        return collect(config('plastic.populate.models'))->get($index, []);
    }

    /**
     * Gets the chunk size.
     *
     * @return int
     */
    protected function chunkSize()
    {
        return (int) config('plastic.populate.chunk_size');
    }
}
