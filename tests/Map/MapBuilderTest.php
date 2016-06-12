<?php

class MapBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_a_map_from_blueprint()
    {
        $connection = Mockery::mock('Sleimanx2\Plastic\Connection')->makePartial();

        $connection->shouldReceive('mapStatement')->once();

        $builder = new \Sleimanx2\Plastic\Map\Builder($connection);

        $builder->create('posts', function ($blueprint) {
            $blueprint->ip('ip');
        });
    }
}
