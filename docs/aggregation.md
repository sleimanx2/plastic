##List of supported aggregations

`average`,`cardinality`,`dateRange`,`geoBounds`,`geoDistance`,`geoHashGrid`,`histogram`,`ipv4Range`,`max`,`min`,`missing`,`percentile`,`percentileRanks`,`stats`,`sum`,`valueCount`,`range`,`terms`

Plastic doesn't support all available aggregations yet like the nested aggregation however you can still implement this functionality.

Knowing that Plastic uses
[`ongr-io/ElasticsearchDSL`](https://github.com/ongr-io/ElasticsearchDSL) to build the queries we can do the following.

```php
Post::search()->aggregate(function($builder){

  $minAggregation = new MinAggregation('min_price', 'resellers.price');
  $nestedAggregation = new NestedAggregation('resellers', 'resellers');
  $nestedAggregation->addAggregation($minAggregation);

  $builder->append($nestedAggregation);

});
```
