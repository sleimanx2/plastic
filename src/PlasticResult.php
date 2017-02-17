<?php

namespace Sleimanx2\Plastic;

use Illuminate\Support\Collection;

class PlasticResult
{
    /**
     * Time needed to execute the query.
     *
     * @var
     */
    protected $took;

    /**
     * Check if the query timed out.
     *
     * @var
     */
    protected $timed_out;

    /**
     * @var
     */
    protected $shards;

    /**
     * Result of the query.
     *
     * @var
     */
    protected $hits;

    /**
     * Total number of hits.
     *
     * @var
     */
    protected $totalHits;

    /**
     * Highest document score.
     *
     * @var
     */
    protected $maxScore;

    /**
     * The aggregations result.
     *
     * @var array|null
     */
    protected $aggregations = null;

    /**
     * _construct.
     *
     * @param array $results
     */
    public function __construct(array $results)
    {
        $this->took = $results['took'];

        $this->timed_out = $results['timed_out'];

        $this->shards = $results['_shards'];

        $this->hits = new Collection($results['hits']['hits']);

        $this->totalHits = $results['hits']['total'];

        $this->maxScore = $results['hits']['max_score'];

        $this->aggregations = isset($results['aggregations']) ? $results['aggregations'] : [];
    }

    /**
     * Total Hits.
     *
     * @return int
     */
    public function totalHits()
    {
        return $this->totalHits;
    }

    /**
     * Max Score.
     *
     * @return float
     */
    public function maxScore()
    {
        return $this->maxScore;
    }

    /**
     * Get Shards.
     *
     * @return array
     */
    public function shards()
    {
        return $this->shards;
    }

    /**
     * Took.
     *
     * @return string
     */
    public function took()
    {
        return $this->took;
    }

    /**
     * Timed Out.
     *
     * @return bool
     */
    public function timedOut()
    {
        return (bool) $this->timed_out;
    }

    /**
     * Get Hits.
     *
     * Get the hits from Elasticsearch
     * results as a Collection.
     *
     * @return Collection
     */
    public function hits()
    {
        return $this->hits;
    }

    /**
     * Set the hits value.
     *
     * @param $values
     */
    public function setHits($values)
    {
        $this->hits = $values;
    }

    /**
     * Get aggregations.
     *
     * Get the raw hits array from
     * Elasticsearch results.
     *
     * @return array
     */
    public function aggregations()
    {
        return $this->aggregations;
    }
}
