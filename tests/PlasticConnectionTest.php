<?php

class PlasticConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_returns_a_map_builder()
    {
        $connection = $this->getConnectionMock();

        $this->assertInstanceOf(Sleimanx2\Plastic\Map\Builder::class, $connection->getMapBuilder());
    }

    /**
     * @test
     */
    public function it_sets_the_default_index()
    {
        $connection = $this->getConnectionMock();

        $connection->setDefaultIndex('custom-index');

        $this->assertEquals('custom-index', $connection->getDefaultIndex());
    }

    /**
     * @test
     */
    public function it_returns_a_map_grammar()
    {
        $connection = $this->getConnectionMock();

        $this->assertInstanceOf(Sleimanx2\Plastic\Map\Grammar::class, $connection->getMapGrammar());
    }

    /**
     * @test
     */
    public function it_returns_a_dsl_query()
    {
        $connection = $this->getConnectionMock();

        $this->assertInstanceOf(ONGR\ElasticsearchDSL\Search::class, $connection->getDSLQuery());
    }

    /**
     * @test
     */
    public function it_returns_an_elastic_search_client()
    {
        $connection = $this->getConnectionMock();

        $this->assertInstanceOf('Elasticsearch\Client', $connection->getClient());
    }

    /**
     * @test
     */
    public function it_returns_the_default_index()
    {
        $connection = $this->getConnectionMock();

        $this->assertEquals('plastic', $connection->getDefaultIndex());
    }

    /**
     * @test
     */
    public function it_executes_a_map_statement_on_client()
    {
        $connection = $this->getConnectionMock();

        $client = Mockery::mock('Elasticsearch\Client');
        $client->shouldReceive('indices->putMapping')->withArgs([['index' => 'plastic']])->andReturn('ok');

        $connection->setClient($client);

        $this->assertEquals('ok', $connection->mapStatement([]));
    }

    /**
     * @test
     */
    public function it_executes_a_search_statement_on_client()
    {
        $connection = $this->getConnectionMock();

        $client = Mockery::mock('Elasticsearch\Client');
        $client->shouldReceive('search')->withArgs([['index' => 'plastic']])->andReturn('ok');

        $connection->setClient($client);

        $this->assertEquals('ok', $connection->searchStatement([]));
    }

    /**
     * @test
     */
    public function it_executes_a_suggest_statement_on_client()
    {
        $connection = $this->getConnectionMock();

        $client = Mockery::mock('Elasticsearch\Client');
        $client->shouldReceive('suggest')->withArgs([['index' => 'plastic']])->andReturn('ok');

        $connection->setClient($client);

        $this->assertEquals('ok', $connection->suggestStatement([]));
    }

    /**
     * @test
     */
    public function it_executes_a_index_statement_on_client()
    {
        $connection = $this->getConnectionMock();

        $client = Mockery::mock('Elasticsearch\Client');
        $client->shouldReceive('index')->withArgs([['index' => 'plastic']])->andReturn('ok');

        $connection->setClient($client);

        $this->assertEquals('ok', $connection->indexStatement([]));
    }

    /**
     * @test
     */
    public function it_executes_an_update_statement_on_client()
    {
        $connection = $this->getConnectionMock();

        $client = Mockery::mock('Elasticsearch\Client');
        $client->shouldReceive('update')->withArgs([['index' => 'plastic']])->andReturn('ok');

        $connection->setClient($client);

        $this->assertEquals('ok', $connection->updateStatement([]));
    }

    /**
     * @test
     */
    public function it_executes_a_delete_statement_on_client()
    {
        $connection = $this->getConnectionMock();

        $client = Mockery::mock('Elasticsearch\Client');
        $client->shouldReceive('delete')->withArgs([['index' => 'plastic']])->andReturn('ok');

        $connection->setClient($client);

        $this->assertEquals('ok', $connection->deleteStatement([]));
    }

    /**
     * @test
     */
    public function it_executes_an_exists_statement_on_client()
    {
        $connection = $this->getConnectionMock();

        $client = Mockery::mock('Elasticsearch\Client');
        $client->shouldReceive('exists')->withArgs([['index' => 'plastic']])->andReturn('ok');

        $connection->setClient($client);

        $this->assertEquals('ok', $connection->existsStatement([]));
    }

    /**
     * @test
     */
    public function it_executes_a_bulk_statement_on_client()
    {
        $connection = $this->getConnectionMock();

        $client = Mockery::mock('Elasticsearch\Client');
        $client->shouldReceive('bulk')->withArgs([['test' => 'test']])->andReturn('ok');

        $connection->setClient($client);

        $this->assertEquals('ok', $connection->bulkStatement(['test' => 'test']));
    }

    /**
     * @test
     */
    public function it_executes_a_statement_with_custom_index()
    {
        $connection = $this->getConnectionMock();

        $client = Mockery::mock('Elasticsearch\Client');
        $client->shouldReceive('update')->withArgs([['index' => 'custom_index']])->andReturn(true);
        $client->shouldReceive('exists')->withArgs([['index' => 'custom_index']])->andReturn(true);
        $client->shouldReceive('delete')->withArgs([['index' => 'custom_index']])->andReturn(true);
        $client->shouldReceive('search')->withArgs([['index' => 'custom_index']])->andReturn(true);
        $client->shouldReceive('indices->putMapping')->withArgs([['index' => 'custom_index']])->andReturn(true);
        $client->shouldReceive('suggest')->withArgs([['index' => 'custom_index']])->andReturn(true);
        $client->shouldReceive('index')->withArgs([['index' => 'custom_index']])->andReturn(true);

        $connection->setClient($client);

        $this->assertTrue($connection->updateStatement(['index' => 'custom_index']));
        $this->assertTrue($connection->existsStatement(['index' => 'custom_index']));
        $this->assertTrue($connection->deleteStatement(['index' => 'custom_index']));
        $this->assertTrue($connection->searchStatement(['index' => 'custom_index']));
        $this->assertTrue($connection->mapStatement(['index' => 'custom_index']));
        $this->assertTrue($connection->suggestStatement(['index' => 'custom_index']));
        $this->assertTrue($connection->indexStatement(['index' => 'custom_index']));
    }

    /**
     * @test
     */
    public function it_starts_a_fluent_search_query_builder()
    {
        $connection = $this->getConnectionMock();

        $this->assertInstanceOf('Sleimanx2\Plastic\DSL\SearchBuilder', $connection->search());
    }

    /**
     * @test
     */
    public function it_starts_a_fluent_suggest_query_builder()
    {
        $connection = $this->getConnectionMock();

        $this->assertInstanceOf('Sleimanx2\Plastic\DSL\SuggestionBuilder', $connection->suggest());
    }

    /**
     * @test
     */
    public function it_can_return_a_persistance_handler_for_eloquent_model()
    {
        $connection = $this->getConnectionMock();

        $this->assertInstanceOf(Sleimanx2\Plastic\Persistence\EloquentPersistence::class, $connection->persist(new TestModel()));
    }

    /**
     * Generate a connection object.
     *
     * @param array $methods
     * @param array $config
     *
     * @return Connection
     */
    protected function getConnectionMock($methods = [], $config = [])
    {
        $config = $config ? $config : ['index' => 'plastic', 'connection' => ['hosts' => ['127.0.0.1:9200']]];

        $methods = $methods ? $methods : null;

        $connection = $this->getMock('Sleimanx2\Plastic\Connection', $methods, [$config]);

        return $connection;
    }
}

class TestModel extends \Illuminate\Database\Eloquent\Model
{
}
