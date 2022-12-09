# PHP SIMPLE CRUD

first we need to initialize DB class

```php
include_once 'DB.php';

class Product extends DB
{
    protected $table = 'products';
}

$product = new Product();

```

## create

for create data we need pass data as array

```php
$product->create([
    'name' => 'koko',
    'email' => 'koko@koko.koko',
    'password' => 'password'
]);
```

## select

for select data from table

```php
$product->where('name', "haha")
    ->orderBy('id', 'asc')
    ->orderBy('name', 'asc')
    ->limit(1, 6)
    ->get();
```

## update

update data

```php
$product->where('id', 2)
    ->update([
        'name' => 'koko',
        'email' => 'koko@koko.koko',
        'password' => 'password'
    ]);
```

## delete

delete data using where condition

```php
$product
    ->where('id', 1)
    ->where('name', "haha")
    ->delete();
```
