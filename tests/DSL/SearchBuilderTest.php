<?php

use Sleimanx2\Plastic\DSL\SearchBuilder;
use Sleimanx2\Plastic\PlasticResult;

class SearchBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_sets_the_type_to_query_from()
    {
        $builder = $this->getBuilder();

        $builder->type('posts');

        $this->assertEquals($builder->getType(), 'posts');
    }

    /**
     * @test
     */
    public function it_sets_the_type_from_a_searchable_model()
    {
        $builder = $this->getBuilder();

        $builder->model(new SearchableModelBuilder());

        $this->assertEquals($builder->getType(), 'searchable_model_builders');
    }

    /**
     * @test
     */
    public function it_sets_the_index_to_query_from()
    {
        $builder = $this->getBuilder();

        $builder->index('custom_index');

        $this->assertEquals($builder->getIndex(), 'custom_index');
    }

    /**
     * @test
     */
    public function it_sets_the_index_from_a_searchable_model()
    {
        $builder = $this->getBuilder();

        $builder->model(new SearchableModelBuilder());

        $this->assertEquals($builder->getIndex(), 'model_index');
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_provided_with_a_none_searchable_model()
    {
        $builder = $this->getBuilder();
        $this->setExpectedException('Sleimanx2\Plastic\Exception\InvalidArgumentException');
        $builder->model(new NotSearchableModelBuilder());
    }

    /**
     * @test
     */
    public function it_sets_the_query_from_attribute()
    {
        $builder = $this->getBuilder();
        $builder->from(1);
        $dsl = $builder->toDSL();
        $this->assertEquals(['from' => 1], $dsl);
    }

    /**
     * @test
     */
    public function it_sets_the_query_size_attribute()
    {
        $builder = $this->getBuilder();
        $builder->size(1);
        $dsl = $builder->toDSL();
        $this->assertEquals(['size' => 1], $dsl);
    }

    /**
     * @test
     */
    public function it_sets_sort_attribute_by_one_or_more_field()
    {
        $builder = $this->getBuilder();
        $builder->sortBy('id', 'desc');
        $dsl = $builder->toDSL();
        $this->assertEquals(['sort' => [['id' => ['order' => 'desc']]]], $dsl);
        $builder = $this->getBuilder();
        $builder->sortBy(['id', 'date'], 'desc');
        $dsl = $builder->toDSL();
        $this->assertEquals(['sort' => [['id' => ['order' => 'desc']], ['date' => ['order' => 'desc']]]], $dsl);
    }

    /**
     * @test
     */
    public function it_sets_the_minScore_attribute()
    {
        $builder = $this->getBuilder();
        $builder->minScore(100);
        $dsl = $builder->toDSL();
        $this->assertEquals(['min_score' => 100], $dsl);
    }

    /**
     * @test
     */
    public function it_toggles_the_bool_query_state()
    {
        $builder = $this->getBuilder();
        $this->assertEquals('must', $builder->getBoolState());

        $builder->should();
        $this->assertEquals('should', $builder->getBoolState());

        $builder->must();
        $this->assertEquals('must', $builder->getBoolState());

        $builder->mustNot();
        $this->assertEquals('must_not', $builder->getBoolState());
    }

    /**
     * @test
     */
    public function it_set_and_ids_query()
    {
        $builder = $this->getBuilder();
        $builder->ids(1);
        $this->assertEquals(['query' => ['ids' => ['values' => [1]]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_term_query()
    {
        $builder = $this->getBuilder();
        $builder->term('name', 'foo');
        $this->assertEquals(['query' => ['term' => ['name' => 'foo']]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_terms_query()
    {
        $builder = $this->getBuilder();
        $builder->terms('name', ['foo', 'bar']);
        $this->assertEquals(['query' => ['terms' => ['name' => ['foo', 'bar']]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_wildcard_query()
    {
        $builder = $this->getBuilder();
        $builder->wildcard('name', 'foo');
        $this->assertEquals(['query' => ['wildcard' => ['name' => ['value' => 'foo', 'boost' => '1.0']]]],
            $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_matchAll_query()
    {
        $builder = $this->getBuilder();
        $builder->matchAll();
        $this->assertEquals(['query' => ['match_all' => ['boost' => '1.0']]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_match_query()
    {
        $builder = $this->getBuilder();
        $builder->match('name', 'foo');
        $this->assertEquals(['query' => ['match' => ['name' => ['query' => 'foo']]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_multiMatch_query()
    {
        $builder = $this->getBuilder();
        $builder->multiMatch(['name', 'bio'], 'foo');
        $this->assertEquals(['query' => ['multi_match' => ['fields' => ['name', 'bio'], 'query' => 'foo']]],
            $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_geBoundingBox_query()
    {
        $builder = $this->getBuilder();
        $builder->geoBoundingBox('geo', [0.2, 3]);
        $this->assertEquals([
            'query' => [
                'geo_bounding_box' => ['geo' => ['top_left' => 0.2, 'bottom_right' => 3]],
            ],
        ], $builder->toDSL());

        $builder = $this->getBuilder();
        $builder->geoBoundingBox('geo', [1, 2, 3, 4]);
        $this->assertEquals([
            'query' => [
                'geo_bounding_box' => [
                    'geo' => [
                        'top'    => 1,
                        'left'   => 2,
                        'bottom' => 3,
                        'right'  => 4,
                    ],
                ],
            ],
        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_geoDistance_query()
    {
        $builder = $this->getBuilder();
        $builder->geoDistance('location', '12km', ['lat' => 1, 'long' => 2]);
        $this->assertEquals([
            'query' => [
                'geo_distance' => [
                    'distance' => '12km',
                    'location' => ['lat' => 1, 'long' => 2],
                ],
            ],
        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_geoDistanceRange_query()
    {
        $builder = $this->getBuilder();
        $builder->geoDistanceRange('location', '0km', '12km', ['lat' => 1, 'long' => 2]);
        $this->assertEquals([
            'query' => [
                'geo_distance_range' => [
                    'from'     => '0km',
                    'to'       => '12km',
                    'location' => ['lat' => 1, 'long' => 2],
                ],
            ],
        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_geoPolygon_query()
    {
        $builder = $this->getBuilder();
        $builder->geoPolygon('location', [1, 2, 3, 4]);
        $this->assertEquals([
            'query' => [
                'geo_polygon' => [
                    'location' => ['points' => [1, 2, 3, 4]],
                ],
            ],
        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_geoShape_query()
    {
        /** @var \Sleimanx2\Plastic\DSL\SearchBuilder $builder */
        $builder = $this->getBuilder();

        $builder->geoShape('area', 'point', [3.3, 33.3]);

        $this->assertEquals([
            'query' => [
                'geo_shape' => [
                    'area' => [
                        'shape' => [
                            'type'        => 'point',
                            'coordinates' => [3.3, 33.3],
                        ],
                        'relation' => 'intersects',
                    ],
                ],
            ],
        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_prefix_query()
    {
        $builder = $this->getBuilder();
        $builder->prefix('name', 'fo');
        $this->assertEquals(['query' => ['prefix' => ['name' => ['value' => 'fo']]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_queryString_query()
    {
        $builder = $this->getBuilder();
        $builder->queryString('fo OR bar');
        $this->assertEquals(['query' => ['query_string' => ['query' => 'fo OR bar']]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_simpleQueryString_query()
    {
        $builder = $this->getBuilder();
        $builder->simpleQueryString('fo OR bar');
        $this->assertEquals(['query' => ['simple_query_string' => ['query' => 'fo OR bar']]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_range_query()
    {
        $builder = $this->getBuilder();
        $builder->range('date', ['gte' => '1974-2-9']);
        $this->assertEquals(['query' => ['range' => ['date' => ['gte' => '1974-2-9']]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_regexp_query()
    {
        $builder = $this->getBuilder();
        $builder->regexp('name', '[1-9]');
        $this->assertEquals(['query' => ['regexp' => ['name' => ['value' => '[1-9]']]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_commonTerm_query()
    {
        $builder = $this->getBuilder();
        $builder->commonTerm('name', 'foo');
        $this->assertEquals(['query' => ['common' => ['name' => ['query' => 'foo']]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_fuzzy_query()
    {
        $builder = $this->getBuilder();
        $builder->fuzzy('name', 'foo');
        $this->assertEquals(['query' => ['fuzzy' => ['name' => ['value' => 'foo']]]], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_nested_query()
    {
        $builder = $this->getBuilder();
        $builder->nested('tag', function ($builder) {
            $builder->term('tag.name', 'foo');
        });

        $this->assertEquals([
            'query' => [
                'nested' => [
                    'path'       => 'tag',
                    'query'      => ['term' => ['tag.name' => 'foo']],
                    'score_mode' => 'avg',
                ],
            ],

        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_bool_query()
    {
        $builder = $this->getBuilder();
        $builder->should()->term('name', 'foo');
        $this->assertEquals([
            'query' => [
                'bool' => [
                    'should' => [['term' => ['name' => 'foo']]],
                ],
            ],

        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_filter_query()
    {
        $builder = $this->getBuilder();
        $builder->filter()->mustNot()->term('name', 'foo');
        $this->assertEquals([
            'query' => [
                'bool' => [
                    'must_not' => [['term' => ['name' => 'foo']]],
                ],
            ],

        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_highlighter()
    {
        $builder = $this->getBuilder();
        $builder->highlight();

        $this->assertEquals(['highlight' => ['pre_tags' => ['<mark>'], 'post_tags' => ['</mark>'], 'fields' => ['_all' => new stdClass()]]], $builder->toDSL());
    }

    /** @test */
    public function it_set_a_decay_function_score()
    {
        $builder = $this->getBuilder();
        $builder->functions(function (SearchBuilder $builder) {
            $builder->matchAll();
        }, function ($builder) {
            $builder->decay('gauss', 'length', [
                'origin' => 5,
                'offset' => 1,
                'scale'  => 4,
            ]);
        });

        $this->assertEquals([
            'query' => [
                'function_score' => [
                    'query'     => ['match_all' => ['boost' => 1.0]],
                    'functions' => [['gauss' => ['length' => ['origin' => 5, 'offset' => 1, 'scale' => 4]]]],
                ],
            ],

        ], $builder->toDSL());
    }

    /** @test */
    public function it_set_a_weight_function_score()
    {
        $builder = $this->getBuilder();
        $builder->functions(function (SearchBuilder $builder) {
            $builder->matchAll();
        }, function ($builder) {
            $builder->weight(3, new \ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery('name', 'abc'));
        });

        $this->assertEquals([
            'query' => [
                'function_score' => [
                    'query'     => ['match_all' => ['boost' => 1.0]],
                    'functions' => [['weight' => 3, 'filter' => ['term' => ['name' => 'abc']]]],
                ],
            ],

        ], $builder->toDSL());
    }

    /** @test */
    public function it_set_a_random_function_score()
    {
        $builder = $this->getBuilder();
        $builder->functions(function (SearchBuilder $builder) {
            $builder->matchAll();
        }, function ($builder) {
            $builder->random(3, new \ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery('name', 'abc'));
        });

        $this->assertEquals([
            'query' => [
                'function_score' => [
                    'query'     => ['match_all' => ['boost' => 1.0]],
                    'functions' => [['random_score' => ['seed' => 3], 'filter' => ['term' => ['name' => 'abc']]]],
                ],
            ],

        ], $builder->toDSL());
    }

    /** @test */
    public function it_set_a_field_function_score()
    {
        $builder = $this->getBuilder();
        $builder->functions(function (SearchBuilder $builder) {
            $builder->matchAll();
        }, function ($builder) {
            $builder->field('name', 2);
        });

        $this->assertEquals([
            'query' => [
                'function_score' => [
                    'query'     => ['match_all' => ['boost' => 1.0]],
                    'functions' => [['field_value_factor' => ['field' => 'name', 'factor' => 2, 'modifier' => 'none']]],
                ],
            ],

        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_executes_the_query_and_returns_the_raw_result()
    {
        $builder = $this->getBuilder();
        $connection = $builder->getConnection();
        $connection->shouldReceive('searchStatement')->with([
            'index' => null,
            'type'  => null,
            'body'  => [],
        ])->andReturn('ok');
        $this->assertEquals('ok', $builder->getRaw());
    }

    /**
     * @test
     */
    public function it_executes_the_query_with_custom_index_and_returns_the_raw_result()
    {
        $builder = $this->getBuilder();
        $builder->index('custom_index');
        $builder->type('custom_type');

        $connection = $builder->getConnection();
        $connection->shouldReceive('searchStatement')->with(['index' => 'custom_index', 'type' => 'custom_type', 'body' => []])->andReturn('ok');
        $this->assertEquals('ok', $builder->getRaw());
    }

    /**
     * @test
     */
    public function it_executes_the_query_and_returns_the_formatted_results()
    {
        $builder = $this->getBuilder();
        $connection = $builder->getConnection();
        $builder->model(new SearchableModelBuilder());
        $filler = Mockery::mock('Sleimanx2\Plastic\Fillers\EloquentFiller');
        $builder->setModelFiller($filler);
        $return = [
            'took'      => '200',
            'timed_out' => false,
            '_shards'   => 2,
            'hits'      => [
                'hits'      => [],
                'total'     => 0,
                'max_score' => 0,
            ],
        ];
        $filler->shouldReceive('fill')->once();

        $connection->shouldReceive('searchStatement')->with([
            'index' => 'model_index',
            'type'  => 'searchable_model_builders',
            'body'  => [],
        ])->andReturn($return);

        $this->assertInstanceOf(PlasticResult::class, $builder->get());
    }

    /**
     * @test
     */
    public function it_paginates_query_result()
    {
        $builder = $this->getBuilder();

        $result = new PlasticResult([
            'took'      => '200',
            'timed_out' => false,
            '_shards'   => 2,
            'hits'      => [
                'hits'      => [],
                'total'     => 0,
                'max_score' => 0,
            ],
        ]);
        $builder->shouldAllowMockingProtectedMethods();
        $builder->shouldReceive('getCurrentPage')->once()->andReturn(1);
        $builder->shouldReceive('from')->once()->with(0)->andReturn($builder);
        $builder->shouldReceive('size')->once()->with(25)->andReturn($builder);
        $builder->shouldReceive('get')->once()->andReturn($result);

        $this->assertInstanceOf(\Sleimanx2\Plastic\PlasticPaginator::class, $builder->paginate());
    }

    /** @test */
    public function it_paginates_set_current_page_query_result()
    {
        $builder = $this->getBuilder();

        $result = new PlasticResult([
            'took'      => '200',
            'timed_out' => false,
            '_shards'   => 2,
            'hits'      => [
                'hits'      => [],
                'total'     => 0,
                'max_score' => 0,
            ],
        ]);
        $builder->shouldAllowMockingProtectedMethods();
        $builder->shouldReceive('getCurrentPage')->once()->with(2)->andReturn(2);
        $builder->shouldReceive('from')->once()->with(25)->andReturn($builder);
        $builder->shouldReceive('size')->once()->with(25)->andReturn($builder);
        $builder->shouldReceive('get')->once()->andReturn($result);

        $this->assertInstanceOf(\Sleimanx2\Plastic\PlasticPaginator::class, $builder->paginate(25, 2));
    }

    /**
     * @test
     */
    public function it_creates_macros()
    {
        $builder = $this->getBuilder();

        $builder->macro('sortByID', function () {
            $this->sortBy('id', 'desc');
        });

        $this->assertTrue($builder->hasMacro('sortByID'));
    }

    private function getBuilder()
    {
        $connection = Mockery::mock('Sleimanx2\Plastic\Connection');
        $query = new \ONGR\ElasticsearchDSL\Search();

        return Mockery::mock('Sleimanx2\Plastic\DSL\SearchBuilder', [$connection, $query])->makePartial();
    }
}

class SearchableModelBuilder extends \Illuminate\Database\Eloquent\Model
{
    use \Sleimanx2\Plastic\Searchable;

    public $documentIndex = 'model_index';
}

class NotSearchableModelBuilder extends \Illuminate\Database\Eloquent\Model
{
}
