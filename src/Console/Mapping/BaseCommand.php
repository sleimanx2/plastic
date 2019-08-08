<?php

namespace Nuwber\Plastic\Console\Mapping;

use Illuminate\Console\Command;

class BaseCommand extends Command
{
    /**
     * Return the full mappings directory path.
     *
     * @return string
     */
    public function getMappingPath()
    {
        return $this->laravel->databasePath().DIRECTORY_SEPARATOR.'mappings';
    }
}
