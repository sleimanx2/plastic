<?php

class SuggestionBuilderTest extends PHPUnit_Framework_TestCase
{
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
    public function it_set_a_completion_suggestion()
    {
        $builder = $this->getBuilder();
        $builder->completion('tags_completion', 'name', 'suggest', ['size' => 3]);

        $this->assertEquals([
            'tags_completion' => [
                'text'       => 'name',
                'completion' => ['field' => 'suggest', 'size' => 3],
            ],
        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_term_suggestion()
    {
        $builder = $this->getBuilder();
        $builder->term('tags_completion', 'name');
        $this->assertEquals([
            'tags_completion' => [
                'text' => 'name',
                'term' => ['field' => '_all'],
            ],
        ], $builder->toDSL());
    }

    /**
     * @test
     */
    public function it_set_a_get_suggestions()
    {
        $builder = $this->getBuilder();
        $builder->shouldReceive('toDSL')->once()->andReturn([]);
        $connection = $builder->getConnection();
        $connection->shouldReceive('suggestStatement')->once()->with(['index' => null, 'body' => []]);
        $builder->get();
    }

    private function getBuilder()
    {
        $connection = Mockery::mock('Nuwber\Plastic\Connection');
        $query = new \ONGR\ElasticsearchDSL\Search();

        return Mockery::mock('Nuwber\Plastic\DSL\SuggestionBuilder', [$connection, $query])->makePartial();
    }
}
