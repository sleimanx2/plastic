<?php

namespace Sleimanx2\Plastic\Mappings;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Mapper
{
    /**
     * @var Mappings
     */
    private $repository;

    /**
     * @var Filesystem
     */
    private $files;

    /**
     * The message notes for the current operation.
     *
     * @var array
     */
    protected $notes;

    /**
     * Mapper constructor.
     *
     * @param Mappings   $repository
     * @param Filesystem $files
     */
    public function __construct(Mappings $repository, Filesystem $files)
    {
        $this->repository = $repository;
        $this->files = $files;
    }

    public function run($path, array $options = [])
    {
        $files = $this->getMappingFiles($path);

        $ran = $this->repository->getRan();

        $mappings = array_diff($files, $ran);

        $this->requireFiles($path, $mappings);

        $this->runMappingList($mappings, $options);
    }

    /**
     * Run an array of mappings.
     *
     * @param array $mappings
     * @param array $options
     */
    public function runMappingList(array $mappings, array $options = [])
    {
        // First we will just make sure that there are any migrations to run. If there
        // aren't, we will just make a note of it to the developer.
        if (count($mappings) == 0) {
            $this->note('<info>Nothing to map</info>');

            return;
        }

        $batch = $this->repository->getNextBatchNumber();

        $step = Arr::get($options, 'step', false);

        foreach ($mappings as $file) {
            $this->runMap($file, $batch);

            if ($step) {
                $batch++;
            }
        }
    }

    /**
     * Run the given mapping file.
     *
     * @param $file
     * @param $batch
     */
    public function runMap($file, $batch)
    {
        $mapping = $this->resolve($file);

        $mapping->map();

        $this->repository->log($file, $batch);

        $this->note('<info>Mapped:</info> '.$file);
    }

    /**
     * Resolve mapping file from.
     *
     * @param $file
     *
     * @return mixed
     */
    public function resolve($file)
    {
        $class = Str::studly($file);

        return new $class();
    }

    /**
     * Get all of the migration files in a given path.
     *
     * @param $path
     *
     * @return array
     */
    public function getMappingFiles($path)
    {
        $files = $this->files->glob($path.'/*_*.php');

        if ($files === false) {
            return [];
        }

        $files = array_map(function ($file) {
            return str_replace('.php', '', basename($file));
        }, $files);

        return $files;
    }

    /**
     * Require All migration files in a given path.
     *
     * @param $path
     * @param array $files
     */
    public function requireFiles($path, array $files)
    {
        foreach ($files as $file) {
            $this->files->requireOnce($path.'/'.$file.'.php');
        }
    }

    /**
     * Check if the mappings repository exists.
     *
     * @return mixed
     */
    public function repositoryExists()
    {
        return $this->repository->exits();
    }

    /**
     * Set the default connection name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setConnection($name)
    {
        $this->repository->setSource($name);
    }

    /**
     * Return the registered notes.
     *
     * @return array
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Return a filesystem instance.
     *
     * @return Filesystem
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Return a filesystem instance.
     *
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->files;
    }

    /**
     * Add a message to the note array.
     *
     * @param $message
     */
    protected function note($message)
    {
        $this->notes[] = $message;
    }
}
