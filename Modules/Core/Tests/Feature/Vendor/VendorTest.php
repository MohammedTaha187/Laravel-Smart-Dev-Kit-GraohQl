<?php

use Modules\Core\Models\Vendor;
use App\Models\User;
use function Pest\Laravel\{getJson, postJson, putJson, patchJson, deleteJson, actingAs};

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $modelKebab = 'vendors'; // Route is often the kebab version of model
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
    $this->vendor = Vendor::factory()->create();
});

it('can list all vendors', function () {
    actingAs($this->admin)
        ->getJson('/api/v1/vendors')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data', 'message']);
});

it('can create a vendor', function () {
    $payload = Vendor::factory()->make()->toArray();

    actingAs($this->admin)
        ->postJson('/api/v1/vendors', $payload)
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data' => ['id']]);
});

it('can show a vendor', function () {
    actingAs($this->admin)
        ->getJson("/api/v1/vendors/{$this->vendor->id}")
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.id', $this->vendor->id);
});

it('can update a vendor', function () {
    $payload = Vendor::factory()->make()->toArray();

    actingAs($this->admin)
        ->putJson("/api/v1/vendors/{$this->vendor->id}", $payload)
        ->assertOk()
        ->assertJsonPath('success', true);
});

it('can delete a vendor', function () {
    actingAs($this->admin)
        ->deleteJson("/api/v1/vendors/{$this->vendor->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('vendors', ['id' => $this->vendor->id]);
});
