<?php

namespace Sleimanx2\Plastic\Console\Mapping;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class ReRun extends Command
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'mapping:rerun {--database=} {--force}';

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
        if (!$this->confirmToProceed()) {
            return;
        }

        $this->call('mapping:reset', [
            '--database' => $this->option('database'),
            '--force'    => true,
        ]);

        $this->call('mapping:run', [
            '--database' => $this->option('database'),
            '--force'    => true,
        ]);
    }
}
