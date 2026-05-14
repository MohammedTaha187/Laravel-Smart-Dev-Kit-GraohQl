<?php

use Modules\Catalog\Models\Product;
use App\Models\User;
use function Pest\Laravel\{getJson, postJson, putJson, patchJson, deleteJson, actingAs};

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $modelKebab = 'products'; // Route is often the kebab version of model
    // Strip trailing 's' if any (Route is plural)
    $singleKey = \Illuminate\Support\Str::singular($modelKebab);

    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    
    $permissions = [
        "view-any-{$singleKey}",
        "view-{$singleKey}",
        "create-{$singleKey}",
        "update-{$singleKey}",
        "delete-{$singleKey}",
    ];

    foreach ($permissions as $p) {
        Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        $role->givePermissionTo($p);
    }

    $this->admin = User::factory()->create()->assignRole('admin');
    $this->product = Product::factory()->create();
});

it('can list all products', function () {
    actingAs($this->admin)
        ->getJson('/api/v1/products')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data', 'message']);
});

it('can create a product', function () {
    $payload = Product::factory()->make()->toArray();

    actingAs($this->admin)
        ->postJson('/api/v1/products', $payload)
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data' => ['id']]);
});

it('can show a product', function () {
    actingAs($this->admin)
        ->getJson("/api/v1/products/{$this->product->id}")
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.id', $this->product->id);
});

it('can update a product', function () {
    $payload = Product::factory()->make()->toArray();

    actingAs($this->admin)
        ->putJson("/api/v1/products/{$this->product->id}", $payload)
        ->assertOk()
        ->assertJsonPath('success', true);
});

it('can delete a product', function () {
    actingAs($this->admin)
        ->deleteJson("/api/v1/products/{$this->product->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('products', ['id' => $this->product->id]);
});
