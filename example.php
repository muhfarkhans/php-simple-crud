<?php

include_once 'DB.php';

class Product extends DB
{
    protected $table = 'products';
}

$product = new Product();

$product->create([
    'name' => 'koko',
    'email' => 'koko@koko.koko',
    'password' => 'password'
]);

$product->where('name', "haha")
    ->orderBy('id', 'asc')
    ->orderBy('name', 'asc')
    ->limit(1, 6)
    ->get();

$product->where('id', 2)
    ->update([
        'name' => 'koko',
        'email' => 'koko@koko.koko',
        'password' => 'password'
    ]);

$product
    ->where('id', 1)
    ->where('name', "haha")
    ->delete();