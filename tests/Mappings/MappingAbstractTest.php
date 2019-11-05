<?php

class MappingAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @tests
     */
    public function it_throws_a_missing_argument_exception_if_missing_model()
    {
        $this->setExpectedException('Nuwber\Plastic\Exception\MissingArgumentException');
        new MappingWithoutModel();
    }

    /**
     * @tests
     */
    public function it_throws_an_invalid_exception_if_the_model_is_not_searchable()
    {
        $this->setExpectedException('Nuwber\Plastic\Exception\InvalidArgumentException');
        new MappingWithNotSearchableModel();
    }

    /**
     * @tests
     */
    public function it_gets_the_type_of_a_searchable_model()
    {
        $mapping = new MappingWithSearchableModel();
        $this->assertEquals('searchable_models', $mapping->getModelType());
    }
}

class MappingWithSearchableModel extends \Nuwber\Plastic\Mappings\Mapping
{
    public $model = 'SearchableModel';
}

class MappingWithNotSearchableModel extends \Nuwber\Plastic\Mappings\Mapping
{
    public $model = 'NotSearchableModel';
}

class MappingWithoutModel extends \Nuwber\Plastic\Mappings\Mapping
{
}

class SearchableModel extends \Illuminate\Database\Eloquent\Model
{
    use \Nuwber\Plastic\Searchable;
}

class NotSearchableModel extends \Illuminate\Database\Eloquent\Model
{
}
