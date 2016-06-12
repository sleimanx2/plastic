<?php

class MappingInstallCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_calls_the_mappings_repository_to_install()
    {
        $command = new \Sleimanx2\Plastic\Console\Mapping\Install($repo = Mockery::mock('Sleimanx2\Plastic\Mappings\Mappings'));
        $command->setLaravel(new \Illuminate\Container\Container());
        $repo->shouldReceive('setSource')->once()->with('foo');
        $repo->shouldReceive('createRepository')->once();
        $this->runCommand($command, ['--database' => 'foo']);
    }

    protected function runCommand($command, $options = [])
    {
        return $command->run(new Symfony\Component\Console\Input\ArrayInput($options), new Symfony\Component\Console\Output\NullOutput());
    }
}
