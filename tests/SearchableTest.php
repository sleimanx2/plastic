<?php

class SearchableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_gets_the_elastic_type_from_table_field_if_type_is_not_set()
    {
        $model = new SearchableModelTest();
        $this->assertEquals('searchable_model_tests', $model->getType());
    }

    /**
     * @test
     */
    public function it_gets_the_elastic_type_from_type_field_if_set()
    {
        $model = new SearchableModelTest();
        $model->type = 'foo';
        $this->assertEquals('foo', $model->getType());
    }

    /**
     * @test
     */
    public function it_gets_the_elastic_document_from_buildDocument_function_if_defined()
    {
        $model = new BuildDocumentSearchableModelTest();
        $this->assertEquals([], $model->getDocumentData());
    }

    /**
     * @test
     */
    public function it_gets_the_elastic_document_from_searchable_array_if_defined()
    {
        $model = new SearchableModelTest();
        $model->searchable = ['id', 'name'];
        $this->assertEquals(['id' => null, 'name' => null], $model->getDocumentData());
    }

    /**
     * @test
     */
    public function it_gets_the_elastic_document_from_self_if_nothing_is_defined()
    {
        $model = new SearchableModelTest();
        $model->id = 1;
        $model->name = 'foo';
        $this->assertEquals(['id' => 1, 'name' => 'foo'], $model->getDocumentData());
    }
}


class SearchableModelTest extends \Illuminate\Database\Eloquent\Model
{
    use \Sleimanx2\Plastic\Searchable;
}

class BuildDocumentSearchableModelTest extends \Illuminate\Database\Eloquent\Model
{
    use \Sleimanx2\Plastic\Searchable;

    public function buildDocument()
    {
        return [];
    }
}
