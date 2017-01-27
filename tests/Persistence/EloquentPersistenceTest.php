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
        $model = new PersistenceModelTest();

        $model->exists = true;

        $connection->shouldReceive('indexStatement')->once()->with([
            'id'    => null,
            'type'  => 'foo',
            'index' => 'bar',
            'body'  => ['foo' => 'bar'],
        ]);
        $persistence = new EloquentPersistence($connection);
        $persistence->model($model);
        $persistence->save();
    }

    /**
     * @test
     */
    public function it_throw_an_exception_if_trying_to_save_a_model_with_exits_false()
    {
        $connection = \Mockery::mock(Connection::class);
        $model = new PersistenceModelTest();

        $model->exists = false;

        $this->setExpectedException('Exception');
        $persistence = new EloquentPersistence($connection);
        $persistence->model($model);
        $persistence->save();
    }

    /**
     * @test
     */
    public function it_updates_a_model_document_data()
    {
        $connection = \Mockery::mock(Connection::class);
        $model = new PersistenceModelTest();

        $model->exists = true;

        $connection->shouldReceive('updateStatement')->once()->with([
            'id'    => null,
            'type'  => 'foo',
            'index' => 'bar',
            'body'  => ['doc' => ['foo' => 'bar']],
        ]);

        $persistence = new EloquentPersistence($connection);
        $persistence->model($model);
        $persistence->update();
    }

    /**
     * @test
     */
    public function it_throw_an_exception_if_trying_to_update_a_model_with_exits_false()
    {
        $connection = \Mockery::mock(Connection::class);
        $model = new PersistenceModelTest();

        $model->exists = false;

        $this->setExpectedException('Exception');
        $persistence = new EloquentPersistence($connection);
        $persistence->model($model);
        $persistence->update();
    }

    /**
     * @test
     */
    public function it_deletes_a_model_document_data()
    {
        $connection = \Mockery::mock(Connection::class);
        $model = new PersistenceModelTest();

        $model->exists = true;

        $connection->shouldReceive('existsStatement')->once()->with([
            'id'    => null,
            'type'  => 'foo',
            'index' => 'bar',
        ])->andReturn(true);

        $connection->shouldReceive('deleteStatement')->once()->with([
            'id'    => null,
            'type'  => 'foo',
            'index' => 'bar',
        ]);
        $persistence = new EloquentPersistence($connection);
        $persistence->model($model);
        $persistence->delete();
    }

    /**
     * @test
     */
    public function it_dosent_execute_a_delete_statement_if_model_document_not_indexed()
    {
        $connection = \Mockery::mock(Connection::class);
        $model = new PersistenceModelTest();

        $model->exists = true;

        $connection->shouldReceive('existsStatement')->once()->with([
            'id'    => null,
            'type'  => 'foo',
            'index' => 'bar',
        ])->andReturn(false);

        $connection->shouldNotReceive('deleteStatement');

        $persistence = new EloquentPersistence($connection);
        $persistence->model($model);
        $persistence->delete();
    }

    /**
     * @test
     */
    public function it_saves_models_data_in_bulk()
    {
        $connection = \Mockery::mock(Connection::class);

        $connection->shouldReceive('getDefaultIndex')->once()->andReturn('plastic');

        $model1 = new PersistenceModelTest();
        $model2 = new PersistenceModelTest();

        $collection = [$model1, $model2];

        $connection->shouldReceive('bulkStatement')->once()->with([
            'body' => [
                [
                    'index' => [
                        '_id'    => null,
                        '_type'  => 'foo',
                        '_index' => 'bar',
                    ],
                ],
                ['foo' => 'bar'],
                [
                    'index' => [
                        '_id'    => null,
                        '_type'  => 'foo',
                        '_index' => 'bar',
                    ],
                ],
                ['foo' => 'bar'],
            ],
        ]);
        $persistence = new EloquentPersistence($connection);
        $persistence->bulkSave($collection);
    }

    /**
     * @test
     */
    public function it_deletes_models_data_in_bulk()
    {
        $connection = \Mockery::mock(Connection::class);
        $connection->shouldReceive('getDefaultIndex')->once()->andReturn('plastic');

        $model1 = new PersistenceModelTest();
        $model2 = new PersistenceModelTest();

        $collection = [$model1, $model2];

        $connection->shouldReceive('bulkStatement')->once()->with([
            'body' => [
                [
                    'delete' => [
                        '_id'    => null,
                        '_type'  => 'foo',
                        '_index' => 'bar',
                    ],
                ],
                [
                    'delete' => [
                        '_id'    => null,
                        '_type'  => 'foo',
                        '_index' => 'bar',
                    ],
                ],
            ],
        ]);

        $persistence = new EloquentPersistence($connection);
        $persistence->bulkDelete($collection);
    }

    /**
     * @test
     */
    public function it_reindex_an_array_of_models_in_bulk()
    {
        $connection = \Mockery::mock(Connection::class);

        $persistence = \Mockery::mock(EloquentPersistence::class, [$connection])->makePartial();

        $persistence->shouldReceive('bulkDelete')->once();
        $persistence->shouldReceive('bulkSave')->once();

        $persistence->reindex([]);
    }
}

class PersistenceModelTest extends Model
{
    use Searchable;

    public $documentType = 'foo';

    public $documentIndex = 'bar';

    public function buildDocument()
    {
        return [
            'foo' => 'bar',
        ];
    }
}
