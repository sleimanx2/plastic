<?php

class MappingRunCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_runs_mappings()
    {
        $mapper = Mockery::mock('Sleimanx2\Plastic\Mappings\Mapper');
        $vendorDir = __DIR__.'/vendor';
        $command = new \Sleimanx2\Plastic\Console\Mapping\Run($mapper, $vendorDir);

        $app = Mockery::mock(new Illuminate\Container\Container())->makePartial();
        $app->shouldReceive('databasePath')->andReturn(__DIR__);
        $app->shouldReceive('environment')->andReturn('local');

        $command->setLaravel($app);

        $mapper->shouldReceive('setConnection')->once()->with(null);
        $mapper->shouldReceive('run')->once()->with(__DIR__.DIRECTORY_SEPARATOR.'mappings', ['step' => false]);
        $mapper->shouldReceive('getNotes')->andReturn([]);
        $mapper->shouldReceive('repositoryExists')->once()->andReturn(true);
        $this->runCommand($command);
    }

    /**
     * @test
     */
    public function it_runs_mappings_in_steps()
    {
        $mapper = Mockery::mock('Sleimanx2\Plastic\Mappings\Mapper');
        $vendorDir = __DIR__.'/vendor';
        $command = new \Sleimanx2\Plastic\Console\Mapping\Run($mapper, $vendorDir);

        $app = Mockery::mock(new Illuminate\Container\Container())->makePartial();
        $app->shouldReceive('databasePath')->andReturn(__DIR__);
        $app->shouldReceive('environment')->andReturn('local');

        $command->setLaravel($app);

        $mapper->shouldReceive('setConnection')->once()->with(null);
        $mapper->shouldReceive('run')->once()->with(__DIR__.DIRECTORY_SEPARATOR.'mappings', ['step' => true]);
        $mapper->shouldReceive('getNotes')->andReturn([]);
        $mapper->shouldReceive('repositoryExists')->once()->andReturn(true);
        $this->runCommand($command, ['--step' => true]);
    }

    /**
     * @test
     */
    public function it_runs_mappings_with_a_given_database()
    {
        $mapper = Mockery::mock('Sleimanx2\Plastic\Mappings\Mapper');
        $vendorDir = __DIR__.'/vendor';
        $command = new \Sleimanx2\Plastic\Console\Mapping\Run($mapper, $vendorDir);

        $app = Mockery::mock(new Illuminate\Container\Container())->makePartial();
        $app->shouldReceive('databasePath')->andReturn(__DIR__);
        $app->shouldReceive('environment')->andReturn('local');

        $command->setLaravel($app);

        $mapper->shouldReceive('setConnection')->once()->with('foo');
        $mapper->shouldReceive('run')->once()->with(__DIR__.DIRECTORY_SEPARATOR.'mappings', ['step' => false]);
        $mapper->shouldReceive('getNotes')->andReturn([]);
        $mapper->shouldReceive('repositoryExists')->once()->andReturn(true);
        $this->runCommand($command, ['--database' => 'foo']);
    }

    protected function runCommand($command, $input = [])
    {
        return $command->run(new Symfony\Component\Console\Input\ArrayInput($input),
            new Symfony\Component\Console\Output\NullOutput());
    }
}
