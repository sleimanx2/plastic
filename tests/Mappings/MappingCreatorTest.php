<?php

class MappingCreatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_create_a_mapping_file()
    {
        $creator = $this->getCreator();

        $creator->getFilesystem()->shouldReceive('get')->once()->with($creator->getStubPath().'/default.stub')->andReturn('DummyClass DummyModel');
        $creator->getFilesystem()->shouldReceive('put')->once()->with('foo/app_user.php', 'AppUser App\User');

        $creator->create('App\User', 'foo');
    }

    protected function getCreator()
    {
        $files = Mockery::mock('Illuminate\Filesystem\Filesystem');

        return new \Sleimanx2\Plastic\Mappings\Creator($files);
    }
}
