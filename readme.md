![Plastic Logo](http://i.imgur.com/PyolY7g.png)
> Plastic is an Elasticsearch ODM and mapper for Laravel.
> It renders the developer experience more enjoyable while using
> Elasticsearch by providing a fluent syntax for mapping , querying and storing eloquent models.

[![Build Status](https://travis-ci.org/sleimanx2/plastic.svg?branch=master)](https://travis-ci.org/sleimanx2/plastic)

##Installing Plastic

```
composer require sleimanx2/plastic
```

```
php artisan vendor:publish
```

This will create a config file at ```config/plastic.php``` and a mapping directory at ```database/mappings```.

Finally we need to add the plastic service provider to ```app/config.php``` under the providers key.

`Sleimanx2\Plastic\PlasticServiceProvider`

##Quick Start

###Defining Searchable Models

To get started lets enable searching capabilities to our model by adding the Searchable
```Sleimanx2\Plastic\Searchable``` trait.

```php
use Sleimanx2\Plastic\Searchable;

class Book extends Model
{
    use Searchable;
}
```
#####Defining what data to store.

In addition to default attributes Plastic provides us with two ways to select which attributes/relations data to store in elastic.

1 - Providing a searchable property to out model

```php
public $searchable = ['id','name','body','tags','images'];
```

2 - Providing a buildDocument method

```php
public function buildDocument()
{
  return [
    'id'=> $this->id,
    'tags'=>$this->tags
  ]
}
```

#####Custom elastic type name

By the default Plastic will use the model table name as the model type however we can customize it by adding a type property to our model.

```php
public $type = 'custom_type';
```

###Storing Model Content

Plastic automatically syncs model data with elastic when you save or delete your model from our SQL DB however this feature can be disable by adding the following property to our model ``` public $syncDocument = false ``` .

> Its important to note that manual document update should be performed in multiple scenarios.
>
> 1 - When we perform a bulk update or delete no Eloquent  event will be triggered thus the document data won't be synced.
>
> 2 - Plastic doesn't listen to related models events yet , so when we update a related model content we should consider updating the parent document.

#####Saving a document

```php
$book = Book::first()->document()->save();
```

#####Partial updating a document

```php
$book = Book::first()->document()->update();
```

#####Deleting a document

```php
$book = Book::first()->document()->delete();
```

#####Saving documents in bulk

```php
$tag = Tag::first();

$tag->document()->blukSave($tag->books);
```

#####Deleting documents in bulk

```php
$author = Author::first();

$author->document()->blukDelete($author->books);
```
#####Reindexing documents in bulk

```php
$post = new Post();

$post->document()->reindex($post->all());
```

###Searching Model Content

Plastic provides a fluent syntax to query our elastic db which leads to a compact readable code lets dig into it.

```php
$result = Book::search()->match('title','pulp')->get();

// Returns a collection of Book Models
$books = $result->hits();

// Returns the total number of matched documents
$result->totalHits();

// Returns the highest query score
$result->maxScore();

//Returns the time needed to execute the query
$result->took();
```

#####Pagination

```php
$books = Book::search()
  ->multiMatch(['title','description'],'ham on rie',['fuzziness' => 'AUTO'])
  ->orderBy('date')
  ->paginate();
```
we still can access the result object after pagination using the result method

```php
$books->result();
```
#####Bool Query

```php
User::search()
  ->must()
    ->term('name','kimchy')
  ->mustNot()
    ->range('age',['from'=>10,'to'=>20]);
  ->should()
    ->match('bio','developer')
    ->match('bio','elastic')
  ->filter()
   ->term('tag','tech')
  ->get();

  // unlike must the matching filter score will be ignore
```

#####Nested Query
```php
$contain = 'foo';

Post::search()
->multiMatch(['title', 'body'], $contain)
->nested('tags', function (SearchBuilder $builder) use ($contain) {
    $builder->match('tags.name', $contain);
});
```

#####Aggregation
```php
$result = User::search()
  ->match('bio','elastic')
  ->aggregate(function(AggregationBuilder $builder){
    $builder->average('average_age','age');
  });

$aggregations = $result->aggregations();
```
#####Suggestions
```php
Plastic::suggest()->completion('tag_suggest', 'photo')->get();
```
suggestions query builder can also be accessed directly from the model as follows
```php
Tag::suggest()->term('tag_term','admin')->get();
```

###Mapping Model

Mappings are an important aspect of elastic we can compare them to indexing in SQL databases. Mapping our models yields to better search results and allows us to use some special query functions like nested and suggestions.

###### generate a model mapping

```
php artisan make:mapping 'App\User'
```

## License

Plastic is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
