<?php

namespace Sleimanx2\Plastic\Console\Mapping;

use Illuminate\Console\Command;
use Sleimanx2\Plastic\Mappings\Mappings;

class Install extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'mapping:install {--database=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the mapping repository';

    /**
     * @var Mappings
     */
    private $repository;

    public function __construct(Mappings $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->repository->setSource($this->option('database'));

        $this->repository->createRepository();

        $this->info('Mapping table created successfully');
    }
}
