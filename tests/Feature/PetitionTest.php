<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Petition;
use App\Models\PetitionProduct;
use App\Models\Warehouse;
use App\Models\Classification;
use App\Models\Price;
use App\Livewire\Petition\Create;
use App\Livewire\Petition\Edit;
use App\Livewire\Petition\Index;
use App\Livewire\Petition\Show;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class PetitionTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;
    private User $regularUser;
    private Customer $customer;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the permissions
        $this->seed(\Database\Seeders\PermisosSeeder::class);

        $this->superadmin = User::where('email', 'superadmin@example.com')->first();
        $this->regularUser = User::where('email', 'usuario@example.com')->first();
        
        $this->customer = Customer::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $classification = Classification::factory()->create(['unit_type' => 'Cantidad']);

        $this->product = Product::factory()->create(['classification_id' => $classification->id]);
        $this->product->productWarehouses()->create(['warehouse_id' => $warehouse->id, 'stock' => 100]);

        Price::factory()->create([
            'product_id' => $this->product->id,
            'customer_id' => $this->customer->id,
            'price_quantity' => 10.00,
        ]);
    }

    /** @test */
    public function the_petition_pages_can_be_rendered()
    {
        $this->actingAs($this->regularUser);
        $this->get(route('petitions.index'))->assertStatus(200);
        $this->get(route('petitions.create'))->assertStatus(200);

        $petition = Petition::create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->regularUser->id,
            'status' => 'Pendiente',
            'total' => 100,
        ]);

        $this->get(route('petitions.edit', $petition))->assertStatus(200);
        $this->get(route('petitions.show', $petition))->assertStatus(200);
    }

    /** @test */
    public function it_can_create_a_valid_petition()
    {
        $this->actingAs($this->regularUser);

        Livewire::test(Create::class)
            ->set('customer_id', $this->customer->id)
            ->set('products.0.product_id', $this->product->id)
            ->set('products.0.quantity', 5)
            ->call('savePetition')
            ->assertRedirect(route('petitions.index'));

        $this->assertDatabaseHas('petitions', [
            'customer_id' => $this->customer->id,
            'user_id' => $this->regularUser->id,
            'status' => 'Pendiente',
        ]);

        $this->assertDatabaseHas('petition_products', [
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);
    }

    /** @test */
    public function customer_is_required_for_petition_creation()
    {
        $this->actingAs($this->regularUser);

        Livewire::test(Create::class)
            ->set('products.0.product_id', $this->product->id)
            ->call('savePetition')
            ->assertHasErrors(['customer_id' => 'required']);
    }

    /** @test */
    public function regular_user_sees_only_their_petitions()
    {
        $this->actingAs($this->regularUser);

        Petition::create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->regularUser->id,
            'status' => 'Pendiente',
            'total' => 100,
        ]);

        $otherPetition = Petition::create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->superadmin->id, // Different user
            'status' => 'Aprobada',
            'total' => 100,
        ]);

        Livewire::test(Index::class)
            ->assertSee('Pendiente')
            ->assertDontSee('Aprobada');
    }

    /** @test */
    public function superadmin_sees_all_petitions()
    {
        $this->actingAs($this->superadmin);

        Petition::create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->regularUser->id,
            'status' => 'Pendiente',
            'total' => 100,
        ]);

        Petition::create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->superadmin->id,
            'status' => 'Aprobada',
            'total' => 100,
        ]);

        Livewire::test(Index::class)
            ->assertSee('Pendiente')
            ->assertSee('Aprobada');
    }

    /** @test */
    public function it_can_update_petition_status()
    {
        $this->actingAs($this->superadmin);
        $petition = Petition::create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->regularUser->id,
            'status' => 'Pendiente',
            'total' => 100,
        ]);

        Livewire::test(Edit::class, ['petition' => $petition])
            ->set('status', 'Aprobada')
            ->call('updatePetition')
            ->assertRedirect(route('petitions.index'));

        $this->assertDatabaseHas('petitions', [
            'id' => $petition->id,
            'status' => 'Aprobada',
        ]);
    }

    /** @test */
    public function it_can_delete_a_petition()
    {
        $this->actingAs($this->superadmin);
        $petition = Petition::create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->regularUser->id,
            'status' => 'Pendiente',
            'total' => 100,
        ]);
        $petitionProduct = $petition->petitionProducts()->create(['product_id' => $this->product->id, 'quantity' => 1, 'price' => 10, 'subtotal' => 10]);

        Livewire::test(Index::class)
            ->call('delete', $petition->id);

        $this->assertModelMissing($petition);
        $this->assertModelMissing($petitionProduct);
    }

    /** @test */
    public function show_page_contains_petition_info_and_can_generate_pdf()
    {
        $this->actingAs($this->regularUser);
        $petition = Petition::create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->regularUser->id,
            'status' => 'Pendiente',
            'total' => 100,
        ]);

        $this->get(route('petitions.show', $petition))
            ->assertSee($this->customer->name);

        Livewire::test(Show::class, ['petition' => $petition])
            ->call('generatePdf')
            ->assertFileDownloaded('petition_' . $petition->id . '.pdf');
    }
}
