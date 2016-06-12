<?php

class MappingMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_runs_remaining_mappings()
    {
        $repository = Mockery::mock(Sleimanx2\Plastic\Mappings\Mappings::class);

        $fileSystem = Mockery::mock(Illuminate\Filesystem\Filesystem::class);

        $mapper = $this->getMock(Sleimanx2\Plastic\Mappings\Mapper::class, ['resolve'], [$repository, $fileSystem]);

        $mapper->getFilesystem()->shouldReceive('glob')->once()->with(__DIR__.'/*_*.php')->andReturn([
            __DIR__.'/2_bar.php',
            __DIR__.'/1_foo.php',
            __DIR__.'/3_baz.php',
        ]);

        $mapper->getFilesystem()->shouldReceive('requireOnce')->with(__DIR__.'/2_bar.php');
        $mapper->getFilesystem()->shouldReceive('requireOnce')->with(__DIR__.'/1_foo.php');
        $mapper->getFilesystem()->shouldReceive('requireOnce')->with(__DIR__.'/3_baz.php');

        $mapper->getRepository()->shouldReceive('getRan')->once()->andReturn(['1_foo']);

        $mapper->getRepository()->shouldReceive('getNextBatchNumber')->once()->andReturn(1);

        $mapper->getRepository()->shouldReceive('log')->once()->with('2_bar', 1);
        $mapper->getRepository()->shouldReceive('log')->once()->with('3_baz', 1);

        $barMock = Mockery::mock('stdClass');
        $barMock->shouldReceive('map')->once();
        $bazMock = Mockery::mock('stdClass');
        $bazMock->shouldReceive('map')->once();

        $mapper->expects($this->at(0))->method('resolve')->with($this->equalTo('2_bar'))->will($this->returnValue($barMock));
        $mapper->expects($this->at(1))->method('resolve')->with($this->equalTo('3_baz'))->will($this->returnValue($bazMock));
        $mapper->run(__DIR__);
    }
}
