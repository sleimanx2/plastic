
##List of supported search method

`from`,`size`,`sortBy`,`minScore`,`ids`,`term`,`terms`,`wildcard`,`matchAll`,`match`,`multiMatch`,`geoBoundingBox`,`geoDistance`,`geoDistanceRange`,`geoHash`,`geoPolygon`,`prefix`,`queryString`,`simpleQueryString`,`range`,`regexp`,`commonTerm`,`fuzzy`,`nested`,`aggregation`

Plastic doesn't support all available search queries yet like the dismax query however you can still implement this functionality.

Knowing that Plastic uses
[`ongr-io/ElasticsearchDSL`](https://github.com/ongr-io/ElasticsearchDSL) to build the queries we can do the following.


```php
$termQuery1 = new TermQuery('age', 34);
$termQuery2 = new TermQuery('age', 35);

$disMaxQuery = new DisMaxQuery();
$disMaxQuery->addParameter('tie_breaker', 0.7);
$disMaxQuery->addParameter('boost', 1.2);
$disMaxQuery->addQuery($termQuery1);
$disMaxQuery->addQuery($termQuery2);

Post::search()->append($disMaxQuery)->get();
```
