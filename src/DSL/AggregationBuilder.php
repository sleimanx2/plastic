<?php

namespace Sleimanx2\Plastic\DSL;

use ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation;
use ONGR\ElasticsearchDSL\Aggregation\AvgAggregation;
use ONGR\ElasticsearchDSL\Aggregation\CardinalityAggregation;
use ONGR\ElasticsearchDSL\Aggregation\DateRangeAggregation;
use ONGR\ElasticsearchDSL\Aggregation\GeoBoundsAggregation;
use ONGR\ElasticsearchDSL\Aggregation\GeoDistanceAggregation;
use ONGR\ElasticsearchDSL\Aggregation\GeoHashGridAggregation;
use ONGR\ElasticsearchDSL\Aggregation\HistogramAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Ipv4RangeAggregation;
use ONGR\ElasticsearchDSL\Aggregation\MaxAggregation;
use ONGR\ElasticsearchDSL\Aggregation\MinAggregation;
use ONGR\ElasticsearchDSL\Aggregation\MissingAggregation;
use ONGR\ElasticsearchDSL\Aggregation\PercentileRanksAggregation;
use ONGR\ElasticsearchDSL\Aggregation\PercentilesAggregation;
use ONGR\ElasticsearchDSL\Aggregation\RangeAggregation;
use ONGR\ElasticsearchDSL\Aggregation\StatsAggregation;
use ONGR\ElasticsearchDSL\Aggregation\SumAggregation;
use ONGR\ElasticsearchDSL\Aggregation\TermsAggregation;
use ONGR\ElasticsearchDSL\Aggregation\ValueCountAggregation;
use ONGR\ElasticsearchDSL\Search as Query;

class AggregationBuilder
{
    /**
     * An instance of DSL query.
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
     * Add an average aggregate.
     *
     * @param $alias
     * @param string|null $field
     * @param string|null $script
     */
    public function average($alias, $field = null, $script = null)
    {
        $aggregation = new AvgAggregation($alias, $field, $script);

        $this->append($aggregation);
    }

    /**
     * Add an cardinality aggregate.
     *
     * @param $alias
     * @param string|null $field
     * @param string|null $script
     * @param int         $precision
     * @param bool        $rehash
     */
    public function cardinality($alias, $field = null, $script = null, $precision = null, $rehash = null)
    {
        $aggregation = new CardinalityAggregation($alias);

        $aggregation->setField($field);

        $aggregation->setScript($script);

        $aggregation->setPrecisionThreshold($precision);

        $aggregation->setRehash($rehash);

        $this->append($aggregation);
    }

    /**
     * Add a date range aggregate.
     *
     * @param $alias
     * @param $field
     * @param $format
     * @param array $ranges
     *
     * @internal param null $from
     * @internal param null $to
     */
    public function dateRange($alias, $field, $format, array $ranges)
    {
        $aggregation = new DateRangeAggregation($alias, $field, $format, $ranges);

        $this->append($aggregation);
    }

    /**
     * Add a geo bounds aggregate.
     *
     * @param string      $alias
     * @param null|string $field
     * @param bool        $wrap_longitude
     */
    public function geoBounds($alias, $field, $wrap_longitude = true)
    {
        $aggregation = new GeoBoundsAggregation($alias, $field, $wrap_longitude);

        $this->append($aggregation);
    }

    /**
     * Add a geo bounds aggregate.
     *
     * @param string      $alias
     * @param null|string $field
     * @param string      $origin
     * @param array       $ranges
     */
    public function geoDistance($alias, $field, $origin, array $ranges)
    {
        $aggregation = new GeoDistanceAggregation($alias, $field, $origin, $ranges);

        $this->append($aggregation);
    }

    /**
     * Add a geo hash grid aggregate.
     *
     * @param string      $alias
     * @param null|string $field
     * @param float       $precision
     * @param null        $size
     * @param null        $shardSize
     */
    public function geoHashGrid($alias, $field, $precision, $size = null, $shardSize = null)
    {
        $aggregation = new GeoHashGridAggregation($alias, $field, $precision, $size, $shardSize);

        $this->append($aggregation);
    }

    /**
     * Add a histogram aggregate.
     *
     * @param $alias
     * @param string $field
     * @param int    $interval
     * @param int    $minDocCount
     * @param string $orderMode
     * @param string $orderDirection
     * @param int    $extendedBoundsMin
     * @param int    $extendedBoundsMax
     * @param bool   $keyed
     */
    public function histogram(
        $alias,
        $field,
        $interval,
        $minDocCount = null,
        $orderMode = null,
        $orderDirection = 'asc',
        $extendedBoundsMin = null,
        $extendedBoundsMax = null,
        $keyed = null
    ) {
        $aggregation = new HistogramAggregation($alias, $field, $interval, $minDocCount, $orderMode, $orderDirection,
            $extendedBoundsMin, $extendedBoundsMax, $keyed);

        $this->append($aggregation);
    }

    /**
     * Add an ipv4 range aggregate.
     *
     * @param $alias
     * @param null  $field
     * @param array $ranges
     */
    public function ipv4Range($alias, $field, array $ranges)
    {
        $aggregation = new Ipv4RangeAggregation($alias, $field, $ranges);

        $this->append($aggregation);
    }

    /**
     * Add an max aggregate.
     *
     * @param $alias
     * @param string|null $field
     * @param string|null $script
     */
    public function max($alias, $field = null, $script = null)
    {
        $aggregation = new MaxAggregation($alias, $field, $script);

        $this->append($aggregation);
    }

    /**
     * Add an min aggregate.
     *
     * @param $alias
     * @param string|null $field
     * @param string|null $script
     */
    public function min($alias, $field = null, $script = null)
    {
        $aggregation = new MinAggregation($alias, $field, $script);

        $this->append($aggregation);
    }

    /**
     * Add an missing aggregate.
     *
     * @param string $alias
     * @param string $field
     */
    public function missing($alias, $field)
    {
        $aggregation = new MissingAggregation($alias, $field);

        $this->append($aggregation);
    }

    /**
     * Add an percentile aggregate.
     *
     * @param $alias
     * @param string $field
     * @param $percents
     * @param null $script
     * @param null $compression
     */
    public function percentile($alias, $field, $percents, $script = null, $compression = null)
    {
        $aggregation = new PercentilesAggregation($alias, $field, $percents, $script, $compression);

        $this->append($aggregation);
    }

    /**
     * Add an percentileRanks aggregate.
     *
     * @param $alias
     * @param string $field
     * @param array  $values
     * @param null   $script
     * @param null   $compression
     */
    public function percentileRanks($alias, $field, array $values, $script = null, $compression = null)
    {
        $aggregation = new PercentileRanksAggregation($alias, $field, $values, $script, $compression);

        $this->append($aggregation);
    }

    /**
     * Add an stats aggregate.
     *
     * @param $alias
     * @param string      $field
     * @param string|null $script
     */
    public function stats($alias, $field = null, $script = null)
    {
        $aggregation = new StatsAggregation($alias, $field, $script);

        $this->append($aggregation);
    }

    /**
     * Add an sum aggregate.
     *
     * @param $alias
     * @param string      $field
     * @param string|null $script
     */
    public function sum($alias, $field = null, $script = null)
    {
        $aggregation = new SumAggregation($alias, $field, $script);

        $this->append($aggregation);
    }

    /**
     * Add a value count aggregate.
     *
     * @param $alias
     * @param string      $field
     * @param string|null $script
     */
    public function valueCount($alias, $field = null, $script = null)
    {
        $aggregation = new ValueCountAggregation($alias, $field, $script);

        $this->append($aggregation);
    }

    /**
     * Add a range aggregate.
     *
     * @param string $alias
     * @param string $field
     * @param array  $ranges
     * @param bool   $keyed
     */
    public function range($alias, $field, array $ranges, $keyed = false)
    {
        $aggregation = new RangeAggregation($alias, $field, $ranges, $keyed);

        $this->append($aggregation);
    }

    /**
     * Add a terms aggregate.
     *
     * @param string      $alias
     * @param string|null $field
     * @param string|null $script
     */
    public function terms($alias, $field = null, $script = null)
    {
        $aggregation = new TermsAggregation($alias, $field, $script);

        $this->append($aggregation);
    }

    /**
     * Return the DSL query.
     *
     * @return array
     */
    public function toDSL()
    {
        return $this->query->toArray();
    }

    /**
     * Append an aggregation to the aggregation query builder.
     *
     * @param AbstractAggregation $aggregation
     */
    public function append(AbstractAggregation $aggregation)
    {
        $this->query->addAggregation($aggregation);
    }
}
