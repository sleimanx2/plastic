<?php

class MappingRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_gets_the_list_of_mappings()
    {
        $repo = $this->getRepository();
        $query = Mockery::mock('stdClass');
        $connectionMock = Mockery::mock('Illuminate\Database\Connection');
        $repo->getConnectionResolver()->shouldReceive('connection')->with(null)->andReturn($connectionMock);
        $repo->getConnection()->shouldReceive('table')->once()->with('mappings')->andReturn($query);
        $query->shouldReceive('orderBy')->once()->with('batch', 'asc')->andReturn($query);
        $query->shouldReceive('orderBy')->once()->with('mapping', 'asc')->andReturn($query);
        $query->shouldReceive('pluck')->once()->with('mapping')->andReturn(['bar']);
        $this->assertEquals(['bar'], $repo->getRan());
    }

    /**
     * @test
     */
    public function it_gets_the_last_bach_of_ran_mappings()
    {
        $repo = $this->getMock('Sleimanx2\Plastic\Mappings\Mappings', ['getLastBatchNumber'], [
            $resolver = Mockery::mock('Illuminate\Database\ConnectionResolverInterface'), 'mappings',
        ]);
        $repo->expects($this->once())->method('getLastBatchNumber')->will($this->returnValue(1));
        $query = Mockery::mock('stdClass');
        $connectionMock = Mockery::mock('Illuminate\Database\Connection');
        $repo->getConnectionResolver()->shouldReceive('connection')->with(null)->andReturn($connectionMock);
        $repo->getConnection()->shouldReceive('table')->once()->with('mappings')->andReturn($query);
        $query->shouldReceive('where')->once()->with('batch', 1)->andReturn($query);
        $query->shouldReceive('orderBy')->once()->with('mapping', 'desc')->andReturn($query);
        $query->shouldReceive('get')->once()->andReturn(['foo']);
        $this->assertEquals(['foo'], $repo->getLast());
    }

    /**
     * @test
     */
    public function it_logs_a_mapping_with_a_batch_number()
    {
        $repo = $this->getRepository();
        $query = Mockery::mock('stdClass');
        $connectionMock = Mockery::mock('Illuminate\Database\Connection');
        $repo->getConnectionResolver()->shouldReceive('connection')->with(null)->andReturn($connectionMock);
        $repo->getConnection()->shouldReceive('table')->once()->with('mappings')->andReturn($query);
        $query->shouldReceive('insert')->once()->with(['mapping' => 'bar', 'batch' => 1]);
        $repo->log('bar', 1);
    }

    /**
     * @test
     */
    public function it_deletes_a_mapping()
    {
        $repo = $this->getRepository();
        $query = Mockery::mock('stdClass');
        $connectionMock = Mockery::mock('Illuminate\Database\Connection');
        $repo->getConnectionResolver()->shouldReceive('connection')->with(null)->andReturn($connectionMock);
        $repo->getConnection()->shouldReceive('table')->once()->with('mappings')->andReturn($query);
        $query->shouldReceive('where')->once()->with('mapping', 'foo')->andReturn($query);
        $query->shouldReceive('delete')->once();
        $mapping = (object) ['mapping' => 'foo'];
        $repo->delete($mapping);
    }

    /**
     * @test
     */
    public function it_gets_the_current_batch_number_plus_one()
    {
        $repo = $this->getMock('Sleimanx2\Plastic\Mappings\Mappings', ['getLastBatchNumber'], [
            Mockery::mock('Illuminate\Database\ConnectionResolverInterface'), 'mappings',
        ]);
        $repo->expects($this->once())->method('getLastBatchNumber')->will($this->returnValue(1));
        $this->assertEquals(2, $repo->getNextBatchNumber());
    }

    /**
     * @test
     */
    public function it_gets_the_last_batch_number()
    {
        $repo = $this->getRepository();
        $query = Mockery::mock('stdClass');
        $connectionMock = Mockery::mock('Illuminate\Database\Connection');
        $repo->getConnectionResolver()->shouldReceive('connection')->with(null)->andReturn($connectionMock);
        $repo->getConnection()->shouldReceive('table')->once()->with('mappings')->andReturn($query);
        $query->shouldReceive('max')->once()->andReturn(1);
        $this->assertEquals(1, $repo->getLastBatchNumber());
    }

    /**
     * @test
     */
    public function testCreateRepositoryCreatesProperDatabaseTable()
    {
        $repo = $this->getRepository();
        $schema = Mockery::mock('stdClass');
        $connectionMock = Mockery::mock('Illuminate\Database\Connection');
        $repo->getConnectionResolver()->shouldReceive('connection')->with(null)->andReturn($connectionMock);
        $repo->getConnection()->shouldReceive('getSchemaBuilder')->once()->andReturn($schema);
        $schema->shouldReceive('create')->once()->with('mappings', Mockery::type('Closure'));
        $repo->createRepository();
    }

    protected function getRepository()
    {
        return new \Sleimanx2\Plastic\Mappings\Mappings(Mockery::mock('Illuminate\Database\ConnectionResolverInterface'), 'mappings');
    }
}
