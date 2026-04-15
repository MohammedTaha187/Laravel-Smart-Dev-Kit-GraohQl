<?php

use App\Models\Product;
use function Pest\Laravel\{getJson, postJson, putJson, deleteJson};

beforeEach(function () {
    //
});

test('can list products', function () {
    Product::factory()->count(3)->create();

    getJson('/api/v1/products')
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('can create product', function () {
    $data = Product::factory()->make()->toArray();

    postJson('/api/v1/products', $data)
        ->assertStatus(201);
});
