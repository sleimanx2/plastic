<?php

class MapBlueprintTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_execute_a_map_statement_from_blueprint()
    {
        $assertion = [
            'index' => null,
            'type'  => 'posts',
            'body'  => ['posts' => ['_source' => ['enabled' => true], 'properties' => ['foo' => 'bar']]],
        ];

        $connection = Mockery::mock(Sleimanx2\Plastic\Connection::class);
        $connection->shouldReceive('mapStatement')->withArgs([$assertion])->once()->andReturn('ok');
        $grammar = Mockery::mock(Sleimanx2\Plastic\Map\Grammar::class);
        $grammar->shouldReceive('compileCreate')->once()->andReturn(['foo' => 'bar']);

        $blueprint = new \Sleimanx2\Plastic\Map\Blueprint('posts');
        $blueprint->create();
        $blueprint->build($connection, $grammar);
    }

    /**
     * @test
     */
    public function it_adds_fields_with_attribute_to_the_fields_property()
    {
        $blueprint = new \Sleimanx2\Plastic\Map\Blueprint('posts');

        $blueprint->addField('foo', 'bar', ['baz' => 'qux']);
        $blueprint->point('coordinate', ['foo' => 'bar']);

        $fields = $blueprint->getFields();

        $this->assertEquals('foo', $fields[0]->type);
        $this->assertEquals('bar', $fields[0]->name);
        $this->assertEquals('qux', $fields[0]->baz);

        $this->assertEquals('point', $fields[1]->type);
        $this->assertEquals('coordinate', $fields[1]->name);
        $this->assertEquals('bar', $fields[1]->foo);
    }
}
