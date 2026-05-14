<?php

use Modules\Sales\Models\Order;
use App\Models\User;
use function Pest\Laravel\{getJson, postJson, putJson, patchJson, deleteJson, actingAs};

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $modelKebab = 'orders'; // Route is often the kebab version of model
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
    $this->order = Order::factory()->create();
});

it('can list all orders', function () {
    actingAs($this->admin)
        ->getJson('/api/v1/orders')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data', 'message']);
});

it('can create a order', function () {
    $payload = Order::factory()->make()->toArray();

    actingAs($this->admin)
        ->postJson('/api/v1/orders', $payload)
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data' => ['id']]);
});

it('can show a order', function () {
    actingAs($this->admin)
        ->getJson("/api/v1/orders/{$this->order->id}")
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.id', $this->order->id);
});

it('can update a order', function () {
    $payload = Order::factory()->make()->toArray();

    actingAs($this->admin)
        ->putJson("/api/v1/orders/{$this->order->id}", $payload)
        ->assertOk()
        ->assertJsonPath('success', true);
});

it('can delete a order', function () {
    actingAs($this->admin)
        ->deleteJson("/api/v1/orders/{$this->order->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('orders', ['id' => $this->order->id]);
});
