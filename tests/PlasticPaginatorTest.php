<?php

class PlasticPaginatorTest extends \PHPUnit_Framework_TestCase
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
    public function it_has_access_to_the_given_result()
    {
        $result = new \Nuwber\Plastic\PlasticResult($this->elasticResult);
        $paginator = new \Nuwber\Plastic\PlasticPaginator($result, 1, 1);
        $this->assertEquals($result, $paginator->result());
    }
}
