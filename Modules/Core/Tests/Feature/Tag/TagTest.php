<?php

use Modules\Core\Models\Tag;
use App\Models\User;
use function Pest\Laravel\{getJson, postJson, putJson, patchJson, deleteJson, actingAs};

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $modelKebab = 'tags'; // Route is often the kebab version of model
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
    $this->tag = Tag::factory()->create();
});

it('can list all tags', function () {
    actingAs($this->admin)
        ->getJson('/api/v1/tags')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data', 'message']);
});

it('can create a tag', function () {
    $payload = Tag::factory()->make()->toArray();

    actingAs($this->admin)
        ->postJson('/api/v1/tags', $payload)
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data' => ['id']]);
});

it('can show a tag', function () {
    actingAs($this->admin)
        ->getJson("/api/v1/tags/{$this->tag->id}")
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.id', $this->tag->id);
});

it('can update a tag', function () {
    $payload = Tag::factory()->make()->toArray();

    actingAs($this->admin)
        ->putJson("/api/v1/tags/{$this->tag->id}", $payload)
        ->assertOk()
        ->assertJsonPath('success', true);
});

it('can delete a tag', function () {
    actingAs($this->admin)
        ->deleteJson("/api/v1/tags/{$this->tag->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('tags', ['id' => $this->tag->id]);
});
