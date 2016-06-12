<?php

namespace Sleimanx2\Plastic\Mappings;

use Closure;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class Creator
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The registered post create hooks.
     *
     * @var array
     */
    protected $postCreate = [];

    /**
     * MappingCreator constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->files = $filesystem;
    }

    /**
     * Create a mapping file.
     *
     * @param $model
     * @param $path
     *
     * @return string
     */
    public function create($model, $path)
    {
        $path = $this->getPath($model, $path);

        $stub = $this->getStub();

        $this->files->put($path, $this->populateStub($model, $stub));

        $this->firePostCreateHooks();

        return $path;
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function getStubPath()
    {
        return __DIR__.'/stubs';
    }

    /**
     * Get the mapping stub template.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->files->get($this->getStubPath().'/default.stub');
    }

    /**
     * Populate the place-holders in the mapping stub.
     *
     * @param string $model
     * @param string $stub
     *
     * @return string
     */
    protected function populateStub($model, $stub)
    {
        $stub = str_replace('DummyClass', $this->getClassName($model), $stub);

        $stub = str_replace('DummyModel', $model, $stub);

        return $stub;
    }

    /**
     * Get the class name of a migration name.
     *
     * @param $model
     *
     * @return string
     */
    protected function getClassName($model)
    {
        return Str::studly(str_replace('\\', '', $model));
    }

    /**
     * Fire the registered post create hooks.
     *
     * @return void
     */
    protected function firePostCreateHooks()
    {
        foreach ($this->postCreate as $callback) {
            call_user_func($callback);
        }
    }

    /**
     * Register a post migration create hook.
     *
     * @param Closure $callback
     *
     * @return void
     */
    public function afterCreate(Closure $callback)
    {
        $this->postCreate[] = $callback;
    }

    /**
     * Return the filesystem instance.
     *
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->files;
    }

    /**
     * Get the full path name to the mapping.
     *
     * @param string $model
     * @param string $path
     *
     * @return string
     */
    protected function getPath($model, $path)
    {
        $name = Str::lower(str_replace('\\', '_', $model));

        return $path.'/'.$name.'.php';
    }
}
