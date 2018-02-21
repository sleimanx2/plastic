##List of available mappings

`string`,`date`,`long`,`integer`,`short`,`byte`,`double`,`binary`,`float`,`boolean`,`point`,`shape`,`ip`,`completion`,`tokenCount`,`nested`

All mappings use the same signature except for the nested mapping lets see some examples.

```php
$map->string('title');

$map->point('location');

$map->shape('area');

$map->nested('tag',function(Blueprint $map){
  $map->string('name');
})
```

We can provide mapping options in two ways

via fluent syntax

```php
$map->point('location')->lat_long(true)->geohash(true);
```

or via method argument

```php
$map->point('location',['lat_long'=>true,'geohash'=>true]);
```
