<?php

use Modules\Core\Models\Address;
use App\Models\User;
use function Pest\Laravel\{getJson, postJson, putJson, patchJson, deleteJson, actingAs};

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $modelKebab = 'addresses'; // Route is often the kebab version of model
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
    $this->address = Address::factory()->create();
});

it('can list all addresses', function () {
    actingAs($this->admin)
        ->getJson('/api/v1/addresses')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data', 'message']);
});

it('can create a address', function () {
    $payload = Address::factory()->make()->toArray();

    actingAs($this->admin)
        ->postJson('/api/v1/addresses', $payload)
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data' => ['id']]);
});

it('can show a address', function () {
    actingAs($this->admin)
        ->getJson("/api/v1/addresses/{$this->address->id}")
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.id', $this->address->id);
});

it('can update a address', function () {
    $payload = Address::factory()->make()->toArray();

    actingAs($this->admin)
        ->putJson("/api/v1/addresses/{$this->address->id}", $payload)
        ->assertOk()
        ->assertJsonPath('success', true);
});

it('can delete a address', function () {
    actingAs($this->admin)
        ->deleteJson("/api/v1/addresses/{$this->address->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('addresses', ['id' => $this->address->id]);
});
