<?php

namespace Sleimanx2\Plastic\Console\Mapping;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Sleimanx2\Plastic\Mappings\Mappings;

class ReRun extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'mapping:rerun {--database=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'remove all mapping log from the repository and run them again';

    /**
     * Rerun constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('mapping:reset', [
            '--database' => $this->option('database')
        ]);

        $this->call('mapping:run', [
            '--database' => $this->option('database')
        ]);
    }
}
