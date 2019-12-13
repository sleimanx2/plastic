
##Inner hits

[`Official documentation`](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-inner-hits.html)

In many cases, it’s very useful to know which inner nested objects (in the case of nested) or children/parent documents (in the case of parent/child) caused certain information to be returned. The inner hits feature can be used for this. This feature returns per search hit in the search response additional nested hits that caused a search hit to match in a different scope.

Plastic also can do this feature, for this you need to specify on nested query the fourth parameter an array of options:


* **from** - The offset from where the first hit to fetch for each inner_hits in the returned regular search hits.
* **size** - The maximum number of hits to return per inner_hits. By default the top three matching hits are returned.
* **sort** - How the inner hits should be sorted per inner_hits. By default the hits are sorted by the score.
* **name** - The name to be used for the particular inner hit definition in the response. Useful when multiple inner hits have been defined in a single search request. The default depends in which query the inner hit is defined. For has_child query and filter this is the child type, has_parent query and filter this is the parent type and the nested query and filter this is the nested path.



```php
$contain = 'foo';

Post::search()
    ->multiMatch(['title', 'body'], $contain)
    ->nested('tags', function (SearchBuilder $builder) use ($contain) {
        $builder->match('tags.name', $contain);
    }, 'avg', ['size' => 10, 'from' => 5, 'sort' => 'desc'])->get();
```
