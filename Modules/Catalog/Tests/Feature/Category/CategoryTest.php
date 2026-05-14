<?php

use Modules\Catalog\Models\Category;
use App\Models\User;
use function Pest\Laravel\{getJson, postJson, putJson, patchJson, deleteJson, actingAs};

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $modelKebab = 'categories'; // Route is often the kebab version of model
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
    $this->category = Category::factory()->create();
});

it('can list all categories', function () {
    actingAs($this->admin)
        ->getJson('/api/v1/categories')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data', 'message']);
});

it('can create a category', function () {
    $payload = Category::factory()->make()->toArray();

    actingAs($this->admin)
        ->postJson('/api/v1/categories', $payload)
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data' => ['id']]);
});

it('can show a category', function () {
    actingAs($this->admin)
        ->getJson("/api/v1/categories/{$this->category->id}")
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.id', $this->category->id);
});

it('can update a category', function () {
    $payload = Category::factory()->make()->toArray();

    actingAs($this->admin)
        ->putJson("/api/v1/categories/{$this->category->id}", $payload)
        ->assertOk()
        ->assertJsonPath('success', true);
});

it('can delete a category', function () {
    actingAs($this->admin)
        ->deleteJson("/api/v1/categories/{$this->category->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('categories', ['id' => $this->category->id]);
});
