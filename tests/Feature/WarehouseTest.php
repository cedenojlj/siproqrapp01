<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Warehouse;
use App\Livewire\Warehouse\Create;
use App\Livewire\Warehouse\Edit;
use App\Livewire\Warehouse\Index;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WarehouseTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function the_warehouse_index_page_can_be_rendered()
    {
        $this->get(route('warehouses.index'))->assertStatus(200);
    }

    /** @test */
    public function the_warehouse_create_page_can_be_rendered()
    {
        $this->get(route('warehouses.create'))->assertStatus(200);
    }

    /** @test */
    public function it_can_create_a_valid_warehouse()
    {
        Livewire::test(Create::class)
            ->set('name', 'Bodega Central')
            ->set('location', 'Calle Principal 123')
            ->call('save')
            ->assertRedirect(route('warehouses.index'));

        $this->assertTrue(Warehouse::where('name', 'Bodega Central')->exists());
    }

    /** @test */
    public function name_is_required_for_creation()
    {
        Livewire::test(Create::class)
            ->set('location', 'Calle Principal 123')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    /** @test */
    public function location_is_required_for_creation()
    {
        Livewire::test(Create::class)
            ->set('name', 'Bodega Central')
            ->call('save')
            ->assertHasErrors(['location']);
    }

    /** @test */
    public function the_warehouse_edit_page_can_be_rendered()
    {
        $warehouse = Warehouse::factory()->create();
        $this->get(route('warehouses.edit', $warehouse))
            ->assertStatus(200)
            ->assertSee($warehouse->name);
    }

    /** @test */
    public function it_can_update_a_warehouse()
    {
        $warehouse = Warehouse::factory()->create();

        Livewire::test(Edit::class, ['warehouse' => $warehouse])
            ->set('name', 'Bodega Actualizada')
            ->set('location', 'Nueva Ubicacion 456')
            ->call('update')
            ->assertRedirect(route('warehouses.index'));

        $this->assertDatabaseHas('warehouses', [
            'id' => $warehouse->id,
            'name' => 'Bodega Actualizada',
            'location' => 'Nueva Ubicacion 456'
        ]);
    }

    /** @test */
    public function it_can_delete_a_warehouse()
    {
        $warehouse = Warehouse::factory()->create();

        Livewire::test(Index::class)
            ->call('delete', $warehouse->id);

        $this->assertModelMissing($warehouse);
    }

    /** @test */
    public function search_by_name_or_location_works_correctly()
    {
        $warehouseA = Warehouse::factory()->create(['name' => 'Bodega Alpha', 'location' => 'Lugar Uno']);
        $warehouseB = Warehouse::factory()->create(['name' => 'Bodega Beta', 'location' => 'Lugar Dos']);

        Livewire::test(Index::class)
            ->set('search', 'Alpha')
            ->assertSee($warehouseA->name)
            ->assertDontSee($warehouseB->name);

        Livewire::test(Index::class)
            ->set('search', 'Lugar Dos')
            ->assertSee($warehouseB->name)
            ->assertDontSee($warehouseA->name);
    }
}
