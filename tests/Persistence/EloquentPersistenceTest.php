<?php

namespace plastic\tests\Persistence;

use Illuminate\Database\Eloquent\Model;
use Sleimanx2\Plastic\Connection;
use Sleimanx2\Plastic\Persistence\EloquentPersistence;
use Sleimanx2\Plastic\Searchable;

class EloquentPersistenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_saves_a_model_document_data()
    {
        $connection = \Mockery::mock(Connection::class);
        $model = \Mockery::mock(PersistenceModelTest::class);

        $model->exists = true;
        $model->shouldReceive('getDocumentData')->once()->andReturn([]);
        $model->shouldReceive('getType')->once()->andReturn('foo');
        $model->shouldReceive('getKey')->once()->andReturn(1);

        $connection->shouldReceive('indexStatement')->once()->with([
            'id'   => 1,
            'type' => 'foo',
            'body' => [],
        ]);
        $persistence = new EloquentPersistence($connection, $model);
        $persistence->save();
    }

    /**
     * @test
     */
    public function it_throw_an_exception_if_trying_to_save_a_model_with_exits_false()
    {
        $connection = \Mockery::mock(Connection::class);
        $model = \Mockery::mock(PersistenceModelTest::class);

        $model->exists = false;

        $this->setExpectedException('Exception');
        $persistence = new EloquentPersistence($connection, $model);
        $persistence->save();
    }

    /**
     * @test
     */
    public function it_updates_a_model_document_data()
    {
        $connection = \Mockery::mock(Connection::class);
        $model = \Mockery::mock(PersistenceModelTest::class);

        $model->exists = true;
        $model->shouldReceive('getDocumentData')->once()->andReturn([]);
        $model->shouldReceive('getType')->once()->andReturn('foo');
        $model->shouldReceive('getKey')->once()->andReturn(1);

        $connection->shouldReceive('updateStatement')->once()->with([
            'id'   => 1,
            'type' => 'foo',
            'body' => ['doc' => []],
        ]);
        $persistence = new EloquentPersistence($connection, $model);
        $persistence->update();
    }

    /**
     * @test
     */
    public function it_throw_an_exception_if_trying_to_update_a_model_with_exits_false()
    {
        $connection = \Mockery::mock(Connection::class);
        $model = \Mockery::mock(PersistenceModelTest::class);

        $model->exists = false;

        $this->setExpectedException('Exception');
        $persistence = new EloquentPersistence($connection, $model);
        $persistence->update();
    }

    /**
     * @test
     */
    public function it_deletes_a_model_document_data()
    {
        $connection = \Mockery::mock(Connection::class);
        $model = \Mockery::mock(PersistenceModelTest::class);

        $model->exists = true;
        $model->shouldReceive('getType')->once()->andReturn('foo');
        $model->shouldReceive('getKey')->once()->andReturn(1);

        $connection->shouldReceive('deleteStatement')->once()->with([
            'id'   => 1,
            'type' => 'foo',
        ]);
        $persistence = new EloquentPersistence($connection, $model);
        $persistence->delete();
    }

    /**
 * @test
 */
    public function it_saves_models_data_in_bulk()
    {
        $connection = \Mockery::mock(Connection::class);

        $connection->shouldReceive('getDefaultIndex')->once()->andReturn('plastic');

        $model = \Mockery::mock(PersistenceModelTest::class);

        $model1 = \Mockery::mock(PersistenceModelTest::class);
        $model1->shouldReceive('getType')->once()->andReturn('foo');
        $model1->shouldReceive('getKey')->once()->andReturn(1);
        $model1->shouldReceive('getDocumentData')->once()->andReturn(['foo' => 'bar']);

        $model2 = \Mockery::mock(PersistenceModelTest::class);
        $model2->shouldReceive('getType')->once()->andReturn('bar');
        $model2->shouldReceive('getKey')->once()->andReturn(2);
        $model2->shouldReceive('getDocumentData')->once()->andReturn(['foo' => 'bar']);

        $collection = [$model1, $model2];

        $connection->shouldReceive('bulkStatement')->once()->with([
            'body' => [
                [
                    'index' => [
                        '_id'    => 1,
                        '_type'  => 'foo',
                        '_index' => 'plastic'
                    ]
                ],
                ['foo' => 'bar'],
                [
                    'index' => [
                        '_id'    => 2,
                        '_type'  => 'bar',
                        '_index' => 'plastic'
                    ]
                ],
                ['foo' => 'bar']
            ]
        ]);
        $persistence = new EloquentPersistence($connection, $model);
        $persistence->bulkSave($collection);
    }

    /**
     * @test
     */
    public function it_deletes_models_data_in_bulk()
    {
        $connection = \Mockery::mock(Connection::class);
        $connection->shouldReceive('getDefaultIndex')->once()->andReturn('plastic');

        $model = \Mockery::mock(PersistenceModelTest::class);

        $model1 = \Mockery::mock(PersistenceModelTest::class);
        $model1->shouldReceive('getType')->once()->andReturn('foo');
        $model1->shouldReceive('getKey')->once()->andReturn(1);

        $model2 = \Mockery::mock(PersistenceModelTest::class);
        $model2->shouldReceive('getType')->once()->andReturn('bar');
        $model2->shouldReceive('getKey')->once()->andReturn(2);

        $collection = [$model1, $model2];

        $connection->shouldReceive('bulkStatement')->once()->with([
            'body' => [
                [
                    'delete' => [
                        '_id'    => 1,
                        '_type'  => 'foo',
                        '_index' => 'plastic'
                    ]
                ],
                [
                    'delete' => [
                        '_id'    => 2,
                        '_type'  => 'bar',
                        '_index' => 'plastic'
                    ]
                ],
            ]
        ]);
        $persistence = new EloquentPersistence($connection, $model);
        $persistence->bulkDelete($collection);
    }


    /**
     * @test
     */
    public function it_reindex_an_array_of_models_in_bulk()
    {
        $connection = \Mockery::mock(Connection::class);
        $model = \Mockery::mock(PersistenceModelTest::class);

        $persistence = \Mockery::mock(EloquentPersistence::class,[$connection, $model])->makePartial();

        $persistence->shouldReceive('bulkDelete')->once();
        $persistence->shouldReceive('bulkSave')->once();

        $persistence->reindex([]);

    }
}

class PersistenceModelTest extends Model
{
    use Searchable;
}