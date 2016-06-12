<?php

namespace Sleimanx2\Plastic\Console\Mapping;

use Illuminate\Support\Composer;
use Sleimanx2\Plastic\Mappings\Creator;

class Make extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:mapping {model : eloquent model full class name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create a new mapping file';

    /**
     * @var Creator
     */
    private $creator;

    /**
     * @var Composer
     */
    private $composer;

    /**
     * Reset constructor.
     *
     * @param Creator  $creator
     * @param Composer $composer
     */
    public function __construct(Creator $creator, Composer $composer)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = trim($this->argument('model'));

        $this->writeMapping($model);

        $this->composer->dumpAutoloads();
    }

    /**
     * Create the mapping file.
     *
     * @param $model
     */
    private function writeMapping($model)
    {
        $path = $this->getMappingPath();

        $file = pathinfo($this->creator->create($model, $path), PATHINFO_FILENAME);

        $this->comment($file.' was created successfully');
    }
}
