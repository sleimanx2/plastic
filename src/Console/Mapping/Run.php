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
    protected $signature = 'mapping:run {--database=} {--step} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'run the remaining mappings';

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * Reset constructor.
     *
     * @param Mapper $mapper
     */
    public function __construct(Mapper $mapper)
    {
        parent::__construct();

        $this->mapper = $mapper;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        $this->prepareDatabase();

        $path = $this->getMappingPath();

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

    protected function prepareDatabase()
    {
        $this->mapper->setConnection($this->option('database'));

        if (!$this->mapper->repositoryExists()) {
            $options = ['--database' => $this->option('database')];

            $this->call('mapping:install', $options);
        }
    }
}
