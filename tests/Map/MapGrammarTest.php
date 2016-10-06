<?php

use Sleimanx2\Plastic\Map\Blueprint;

class MapGrammarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_adds_an_integer_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->integer('count');
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['count' => ['type' => 'integer']], $statement);
    }

    /**
     * @test
     */
    public function it_adds_a_long_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->long('facebook_id');
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['facebook_id' => ['type' => 'long']], $statement);
    }

    /**
     * @test
     */
    public function it_adds_a_short_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->short('age');
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['age' => ['type' => 'short']], $statement);
    }

    /**
     * @test
     */
    public function it_adds_a_byte_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->byte('size');
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['size' => ['type' => 'byte']], $statement);
    }

    /**
     * @test
     */
    public function it_adds_a_double_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->double('temp');
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['temp' => ['type' => 'double']], $statement);
    }

    /**
     * @test
     */
    public function it_adds_a_binary_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->binary('binary')->store(true)->doc_values('foo');
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['binary' => ['type' => 'binary', 'store' => true, 'doc_values' => 'foo']], $statement);
    }

    /**
     * @test
     */
    public function it_adds_a_float_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->float('currency');
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['currency' => ['type' => 'float']], $statement);
    }

    /**
     * @test
     */
    public function it_adds_a_date_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->date('created_at');
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['created_at' => ['type' => 'date']], $statement);
    }

    /**
     * @test
     */
    public function it_adds_a_boolean_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->boolean('deleted');
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['deleted' => ['type' => 'boolean']], $statement);
    }

    /**
     * @test
     */
    public function it_adds_a_geo_point_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->point('coordinate');
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['coordinate' => ['type' => 'geo_point']], $statement);
    }

    /**
     * @test
     */
    public function it_adds_a_geo_shape_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->shape('area');
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['area' => ['type' => 'geo_shape']], $statement);
    }

    /**
     * @test
     */
    public function it_adds_an_ip_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->ip('ip');
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['ip' => ['type' => 'ip']], $statement);
    }

    /**
     * @test
     */
    public function it_adds_a_completion_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->completion('suggest');
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['suggest' => ['type' => 'completion']], $statement);
    }

    /**
     * @test
     */
    public function it_adds_a_token_count_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->tokenCount('token');
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['token' => ['type' => 'token_count']], $statement);
    }

    /**
     * @test
     */
    public function it_adds_a_string_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->string('body', ['analyzer' => 'foo', 'boost' => true]);
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['body' => ['type' => 'string', 'analyzer' => 'foo', 'boost' => true]], $statement);
    }

    /**
     * @test
     */
    public function it_adds_a_nested_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->nested('tags', function ($blueprint) {
            $blueprint->string('name');
        });
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['tags' => ['type' => 'nested', 'properties' => ['name' => ['type' => 'string']]]],
            $statement);
    }

    /**
     * @test
     */
    public function it_adds_a_object_map()
    {
        $blueprint = new Blueprint('post');
        $blueprint->create();
        $blueprint->object('tags', function ($blueprint) {
            $blueprint->string('name');
        });
        $statement = $blueprint->toDSL($this->getGrammar());
        $this->assertEquals(['tags' => ['properties' => ['name' => ['type' => 'string']]]],
            $statement);
    }

    protected function getGrammar()
    {
        return new \Sleimanx2\Plastic\Map\Grammar();
    }
}
