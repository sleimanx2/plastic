<?php

namespace Sleimanx2\Plastic\Console\Mapping;

use Illuminate\Console\ConfirmableTrait;
use Sleimanx2\Plastic\Mappings\Mapper;

class Run extends BaseCommand
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'mapping:run {--database=} {--path=} {--force} {--step}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "remove all mapping log from the repository";

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * Reset constructor
     *
     * @param Mapper $mapper
     */
    public function __construct(Mapper $mapper)
    {
        parent::__construct();

        $this->mapper = $mapper;
    }

    /**
     * Execute the console command
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        $this->prepareDatabase();

        // Next, we will check to see if a path option has been defined. If it has
        // we will use the path relative to the root of this installation folder
        // so that migrations may be run for any path within the applications.
        if (!is_null($path = $this->input->getOption('path'))) {
            $path = $this->laravel->basePath() . '/' . $path;
        } else {
            $path = $this->getMappingPath();
        }

        $this->mapper->run($path, [
            'step' => $this->option('step'),
        ]);

        // Once the mapper has run we will grab the note output and send it out to
        // the console screen, since the mapper itself functions without having
        // any instances of the OutputInterface contract passed into the class.
        foreach ($this->mapper->getNotes() as $note) {
            $this->output->writeln($note);
        }

    }


    /**
     *
     */
    protected function prepareDatabase()
    {
        $this->mapper->setConnection($this->option('database'));

        if (!$this->mapper->repositoryExists()) {

            $options = ['--database' => $this->option('database')];

            $this->call('mapping:install', $options);
        }
    }
}
