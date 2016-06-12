<?php

class PlasticResultTest extends \PHPUnit_Framework_TestCase
{
    protected $elasticResult = [
        'took'         => 0.2,
        'timed_out'    => false,
        '_shards'      => 2,
        'hits'         => [
            'total'     => 2,
            'max_score' => 3,
            'hits'      => ['foo', 'bar'],

        ],
        'aggregations' => ['aggregations'],
    ];

    /**
     * @test
     */
    public function it_gets_the_number_of_total_hits()
    {
        $result = new \Sleimanx2\Plastic\PlasticResult($this->elasticResult);
        $this->assertEquals(2, $result->totalHits());
    }

    /**
     * @test
     */
    public function it_gets_the_maxScore()
    {
        $result = new \Sleimanx2\Plastic\PlasticResult($this->elasticResult);
        $this->assertEquals(3, $result->maxScore());
    }

    /**
     * @test
     */
    public function it_gets_the_hits()
    {
        $result = new \Sleimanx2\Plastic\PlasticResult($this->elasticResult);
        $this->assertEquals(['foo', 'bar'], $result->hits()->all());
    }

    /**
     * @test
     */
    public function it_gets_the_query_time_to_execute()
    {
        $result = new \Sleimanx2\Plastic\PlasticResult($this->elasticResult);
        $this->assertEquals(0.2, $result->took());
    }

    /**
     * @test
     */
    public function it_gets_if_the_query_timed_out()
    {
        $result = new \Sleimanx2\Plastic\PlasticResult($this->elasticResult);
        $this->assertEquals(false, $result->timedOut());
    }

    /**
     * @test
     */
    public function it_gets_the_query_aggregations()
    {
        $result = new \Sleimanx2\Plastic\PlasticResult($this->elasticResult);
        $this->assertEquals(['aggregations'], $result->aggregations());
    }

    /**
     * @test
     */
    public function it_gets_the_query_shards()
    {
        $result = new \Sleimanx2\Plastic\PlasticResult($this->elasticResult);
        $this->assertEquals(2, $result->shards());
    }

    /**
     * @test
     */
    public function it_sets_if_the_query_hits()
    {
        $result = new \Sleimanx2\Plastic\PlasticResult($this->elasticResult);
        $result->setHits(['baz']);
        $this->assertEquals(['baz'], $result->hits());
    }
}
