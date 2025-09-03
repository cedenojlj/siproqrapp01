<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Classification;
use App\Models\Warehouse;
use App\Livewire\Product\Create;
use App\Livewire\Product\Edit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Classification $classification;
    private Warehouse $warehouse;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->classification = Classification::factory()->create();
        $this->warehouse = Warehouse::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function the_product_index_page_can_be_rendered()
    {
        $this->get(route('products.index'))->assertStatus(200);
    }

    /** @test */
    public function the_product_create_page_can_be_rendered()
    {
        $this->get(route('products.create'))->assertStatus(200);
    }

    /** @test */
    public function it_can_create_a_valid_product()
    {
        Livewire::test(Create::class)
            ->set('name', 'New Product Name')
            ->set('sku', 'UNIQUE-SKU-123')
            ->set('type', 'Type A')
            ->set('size', 'Large')
            ->set('GN', '100')
            ->set('GW', '120')
            ->set('Box', 'Box-001')
            ->set('invoice_number', 'INV-001')
            ->set('classification_id', $this->classification->id)
            ->set('warehouse_id', $this->warehouse->id)
            ->set('cantidad', 5)
            ->call('save')
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'New Product Name',
            'sku' => 'UNIQUE-SKU-123',
        ]);
    }

    /** @test */
    public function creation_fails_with_missing_name()
    {
        Livewire::test(Create::class)
            ->set('sku', 'UNIQUE-SKU-123')
            ->set('type', 'Type A')
            ->set('size', 'Large')
            ->set('GN', '100')
            ->set('GW', '120')
            ->set('Box', 'Box-001')
            ->set('invoice_number', 'INV-001')
            ->set('classification_id', $this->classification->id)
            ->set('warehouse_id', $this->warehouse->id)
            ->set('cantidad', 5)
            ->call('save')
            ->assertHasErrors(['name']);
    }

    /** @test */
    public function creation_fails_with_duplicate_sku()
    {
        Product::factory()->create(['sku' => 'DUPLICATE-SKU']);

        Livewire::test(Create::class)
            ->set('name', 'Another Product')
            ->set('sku', 'DUPLICATE-SKU')
            ->set('type', 'Type B')
            ->set('size', 'Small')
            ->set('GN', '50')
            ->set('GW', '60')
            ->set('Box', 'Box-002')
            ->set('invoice_number', 'INV-002')
            ->set('classification_id', $this->classification->id)
            ->set('warehouse_id', $this->warehouse->id)
            ->set('cantidad', 10)
            ->call('save')
            ->assertHasErrors(['sku']);
    }

    /** @test */
    public function the_product_edit_page_can_be_rendered()
    {
        $product = Product::factory()->create();
        $this->get(route('products.edit', $product))
            ->assertStatus(200)
            ->assertSee($product->name);
    }

    /** @test */
    public function it_can_update_a_product_name_and_nullable_fields()
    {
        $product = Product::factory()->create([
            'name' => 'Old Name',
            'GN' => 'Old GN',
            'GW' => 'Old GW',
            'Box' => 'Old Box',
            'invoice_number' => 'Old INV',
            'classification_id' => $this->classification->id,
        ]);

        $newClassification = Classification::factory()->create();

        Livewire::test(Edit::class, ['product' => $product])
            ->set('name', 'Updated Name')
            ->set('GN', 'New GN')
            ->set('GW', 'New GW')
            ->set('Box', 'New Box')
            ->set('invoice_number', 'New INV')
            ->set('classification_id', $newClassification->id)
            ->call('update')
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'GN' => 'New GN',
            'GW' => 'New GW',
            'Box' => 'New Box',
            'invoice_number' => 'New INV',
            'classification_id' => $newClassification->id,
        ]);

        // Assert that SKU, Type, Size were NOT updated as per component logic
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'sku' => $product->sku,
            'type' => $product->type,
            'size' => $product->size,
        ]);
    }

    /** @test */
    public function update_fails_with_missing_name()
    {
        $product = Product::factory()->create();

        Livewire::test(Edit::class, ['product' => $product])
            ->set('name', '') // Set to empty string to trigger required validation
            ->call('update')
            ->assertHasErrors(['name']);
    }
}