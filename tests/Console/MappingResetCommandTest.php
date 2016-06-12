<?php

class MappingResetCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_truncates_the_mappings_table()
    {
        $repo = Mockery::mock('Sleimanx2\Plastic\Mappings\Mappings');
        $vendorDir = __DIR__.'/vendor';
        $command = new \Sleimanx2\Plastic\Console\Mapping\Reset($repo, $vendorDir);

        $app = Mockery::mock(new Illuminate\Container\Container())->makePartial();
        $app->shouldReceive('environment')->andReturn('local');
        $repo->shouldReceive('setSource')->once()->with(null);

        $command->setLaravel($app);
        $repo->shouldReceive('reset')->once()->andReturn('ok');
        $this->runCommand($command);
    }

    /**
     * @test
     */
    public function it_truncates_the_mappings_table_with_a_given_database()
    {
        $repo = Mockery::mock('Sleimanx2\Plastic\Mappings\Mappings');
        $vendorDir = __DIR__.'/vendor';
        $command = new \Sleimanx2\Plastic\Console\Mapping\Reset($repo, $vendorDir);

        $app = Mockery::mock(new Illuminate\Container\Container())->makePartial();
        $app->shouldReceive('environment')->andReturn('local');
        $repo->shouldReceive('setSource')->once()->with('foo');

        $command->setLaravel($app);
        $repo->shouldReceive('reset')->once()->andReturn('ok');
        $this->runCommand($command, ['--database' => 'foo']);
    }

    protected function runCommand($command, $input = [])
    {
        return $command->run(new Symfony\Component\Console\Input\ArrayInput($input), new Symfony\Component\Console\Output\NullOutput());
    }
}
