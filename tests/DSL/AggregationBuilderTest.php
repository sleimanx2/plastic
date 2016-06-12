<?php


class AggregationBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_sets_a_cardinality_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->cardinality('foo', 'bar');
        $this->assertEquals(['aggregations' => ['foo' => ['cardinality' => ['field' => 'bar']]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_an_average_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->average('foo', 'bar');
        $this->assertEquals(['aggregations' => ['foo' => ['avg' => ['field' => 'bar']]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_a_dateRange_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->dateRange('foo', 'bar', 'yyyy-m-d', [['from' => '2001-3-3'], ['to' => '2002-3-3']]);
        $this->assertEquals([
            'aggregations' => [
                'foo' => [
                    'date_range' => [
                        'format' => 'yyyy-m-d',
                        'field'  => 'bar',
                        'ranges' => [
                            ['from' => '2001-3-3'],
                            ['to' => '2002-3-3'],
                        ],
                    ],
                ],
            ],
        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_a_geoBounds_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->geoBounds('foo', 'bar');
        $this->assertEquals([
            'aggregations' => [
                'foo' => [
                    'geo_bounds' => [
                        'field'          => 'bar',
                        'wrap_longitude' => true,
                    ],
                ],
            ],
        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_a_geoDistance_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->geoDistance('foo', 'bar', 'baz', [['from' => 'x'], ['to' => 'y']]);
        $this->assertEquals([
            'aggregations' => [
                'foo' => [
                    'geo_distance' => [
                        'origin' => 'baz',
                        'field'  => 'bar',
                        'ranges' => [
                            ['from' => 'x'],
                            ['to' => 'y'],
                        ],
                    ],
                ],
            ],
        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_a_geoHashGrid_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->geoHashGrid('foo', 'bar', 'baz');
        $this->assertEquals([
            'aggregations' => [
                'foo' => [
                    'geohash_grid' => [
                        'precision' => 'baz',
                        'field'     => 'bar',
                    ],
                ],
            ],
        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_a_histogram_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->histogram('foo', 'bar', 2);
        $this->assertEquals([
            'aggregations' => [
                'foo' => [
                    'histogram' => [
                        'interval' => '2',
                        'field'    => 'bar',
                    ],
                ],
            ],
        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_an_ipv4Range_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->ipv4Range('foo', 'bar', [['from' => 1, 'to' => 2]]);
        $this->assertEquals([
            'aggregations' => [
                'foo' => [
                    'ip_range' => [
                        'field'  => 'bar',
                        'ranges' => [['from' => 1, 'to' => 2]],

                    ],
                ],
            ],
        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_a_max_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->max('foo', 'bar');
        $this->assertEquals(['aggregations' => ['foo' => ['max' => ['field' => 'bar']]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_a_min_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->min('foo', 'bar');
        $this->assertEquals(['aggregations' => ['foo' => ['min' => ['field' => 'bar']]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_a_missing_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->missing('foo', 'bar');
        $this->assertEquals(['aggregations' => ['foo' => ['missing' => ['field' => 'bar']]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_a_percentile_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->percentile('foo', 'bar', 50);
        $this->assertEquals(['aggregations' => ['foo' => ['percentiles' => ['field' => 'bar', 'percents' => 50]]]],
            $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_a_percentileRanks_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->percentileRanks('foo', 'bar', ['50']);
        $this->assertEquals(['aggregations' => ['foo' => ['percentile_ranks' => ['field' => 'bar', 'values' => [50]]]]],
            $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_a_stats_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->stats('foo', 'bar');
        $this->assertEquals(['aggregations' => ['foo' => ['stats' => ['field' => 'bar']]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_a_sum_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->sum('foo', 'bar');
        $this->assertEquals(['aggregations' => ['foo' => ['sum' => ['field' => 'bar']]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_a_valueCount_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->valueCount('foo', 'bar');
        $this->assertEquals(['aggregations' => ['foo' => ['value_count' => ['field' => 'bar']]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_a_range_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->range('foo', 'bar', [['from' => 1], ['to' => 2]]);
        $this->assertEquals([
            'aggregations' => [
                'foo' => [
                    'range' => [
                        'field'  => 'bar',
                        'keyed'  => false,
                        'ranges' => [['from' => 1], ['to' => 2]],
                    ],
                ],
            ],
        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_sets_a_terms_aggregation()
    {
        $builder = $this->getBuilder();
        $builder->terms('foo', 'bar');
        $this->assertEquals(['aggregations' => ['foo' => ['terms' => ['field' => 'bar']]]], $builder->toDSL());
    }

    private function getBuilder()
    {
        $query = new \ONGR\ElasticsearchDSL\Search();

        return new Sleimanx2\Plastic\DSL\AggregationBuilder($query);
    }
}
