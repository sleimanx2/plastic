<?php

namespace Sleimanx2\Plastic\DSL;

use ONGR\ElasticsearchDSL\Search as Query;
use ONGR\ElasticsearchDSL\Suggest\CompletionSuggest;
use ONGR\ElasticsearchDSL\Suggest\TermSuggest;

class SuggestionBuilder
{
    /**
     * An instance of DSL query
     *
     * @var Query
     */
    public $query;

    /**
     * Builder constructor.
     *
     * @param Query $query
     */
    public function __construct(Query $query = null)
    {
        $this->query = $query;
    }

    /**
     * Add a completion suggestion
     *
     * @param $name
     * @param $text
     * @param array $parameters
     */
    public function completion($name, $text, $parameters = [])
    {
        $suggestion = new CompletionSuggest($name, $text, $parameters);

        $this->append($suggestion);
    }

    /**
     * Add a term suggestion
     *
     * @param string $name
     * @param string $text
     * @param array $parameters
     */
    public function term($name, $text, array $parameters = [])
    {
        $suggestion = new TermSuggest($name, $text, $parameters = []);

        $this->append($suggestion);
    }

    /**
     * Append a suggestion to query
     *
     * @param $suggestion
     */
    public function append($suggestion)
    {
        $this->query->addSuggest($suggestion);
    }
}