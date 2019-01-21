<?php

class MapBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_a_map_from_blueprint()
    {
        $connection = Mockery::mock('LoRDFM\Plastic\Connection')->makePartial();

        $connection->shouldReceive('mapStatement')->once();

        $builder = new \LoRDFM\Plastic\Map\Builder($connection);

        $builder->create('posts', function ($blueprint) {
            $blueprint->ip('ip');
        });
    }
}
