<?php

class MappingMakeCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_makes_a_mapping_file_and_dumps_autoload()
    {
        $creator = Mockery::mock('Sleimanx2\Plastic\Mappings\Creator');
        $composer = Mockery::mock('Illuminate\Support\Composer');
        $vendorDir = __DIR__.'/vendor';
        $command = new \Sleimanx2\Plastic\Console\Mapping\Make($creator, $composer, $vendorDir);
        $app = Mockery::mock(new Illuminate\Container\Container())->makePartial();
        $app->shouldReceive('databasePath')->andReturn(__DIR__);
        $command->setLaravel($app);
        $creator->shouldReceive('create')->once()->with('App\User', __DIR__.DIRECTORY_SEPARATOR.'mappings');
        $composer->shouldReceive('dumpAutoloads')->once();
        $this->runCommand($command, ['model' => 'App\User']);
    }

    protected function runCommand($command, $input = [])
    {
        return $command->run(new Symfony\Component\Console\Input\ArrayInput($input), new Symfony\Component\Console\Output\NullOutput());
    }
}
