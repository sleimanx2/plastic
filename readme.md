![Plastic Logo](http://i.imgur.com/PyolY7g.png)
> Plastic is an Elasticsearch ODM and mapper for Laravel.
> It renders the developer experience more enjoyable while using
> Elasticsearch by providing a fluent syntax for mapping , querying and storing eloquent models.

##Installing Plastic

```
composer require sleimanx2/plastic
```

```
php artisan vendor:publish
```

This will create a config file at ```config/plastic.php``` and a mapping directory at ```database/mappings```.

##Quick Start

###Defining Searchable Models

To get started lets enable searching capabilities to our model by adding the Searchable ``` Sleimanx2\Plastic\Searchable``` trait.

```php
use Sleimanx2\Plastic\Searchable;

class Book extends Model
{
    use Searchable;
}
```

#####Custom elastic type name

By the default Plastic will use the model table name as the model type however we can customize it by adding a type field to our model.

```
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
