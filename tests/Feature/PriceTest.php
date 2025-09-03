<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Price;
use App\Livewire\Price\Create;
use App\Livewire\Price\Edit;
use App\Livewire\Price\Index;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PriceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product;
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create();
        $this->customer = Customer::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function the_price_index_page_can_be_rendered()
    {
        $this->get(route('prices.index'))->assertStatus(200);
    }

    /** @test */
    public function the_price_create_page_can_be_rendered()
    {
        $this->get(route('prices.create'))->assertStatus(200);
    }

    /** @test */
    public function it_can_create_a_valid_price()
    {
        Livewire::test(Create::class)
            ->set('product_id', $this->product->id)
            ->set('customer_id', $this->customer->id)
            ->set('price_quantity', 100.50)
            ->set('price_weight', 200.75)
            ->call('save')
            ->assertRedirect(route('prices.index'));

        $this->assertTrue(Price::where('product_id', $this->product->id)
                                ->where('customer_id', $this->customer->id)
                                ->exists());
    }

    /** @test */
    public function product_id_is_required_for_creation()
    {
        Livewire::test(Create::class)
            ->set('customer_id', $this->customer->id)
            ->set('price_quantity', 100.50)
            ->set('price_weight', 200.75)
            ->call('save')
            ->assertHasErrors(['product_id']);
    }

    /** @test */
    public function price_quantity_must_be_numeric()
    {
        Livewire::test(Create::class)
            ->set('product_id', $this->product->id)
            ->set('customer_id', $this->customer->id)
            ->set('price_quantity', 'not-a-number')
            ->set('price_weight', 200.75)
            ->call('save')
            ->assertHasErrors(['price_quantity' => 'numeric']);
    }

    /** @test */
    public function the_price_edit_page_can_be_rendered()
    {
        $price = Price::factory()->create();
        $this->get(route('prices.edit', $price))
            ->assertStatus(200)
            ->assertSee($price->price_quantity);
    }

    /** @test */
    public function it_can_update_a_price()
    {
        $price = Price::factory()->create();
        $newCustomer = Customer::factory()->create();

        Livewire::test(Edit::class, ['price' => $price])
            ->set('price_quantity', 999.99)
            ->set('customer_id', $newCustomer->id)
            ->call('update')
            ->assertRedirect(route('prices.index'));

        $this->assertDatabaseHas('prices', [
            'id' => $price->id,
            'price_quantity' => 999.99,
            'customer_id' => $newCustomer->id
        ]);
    }

    /** @test */
    public function it_can_delete_a_price()
    {
        $price = Price::factory()->create();

        Livewire::test(Index::class)
            ->call('delete', $price->id);

        $this->assertModelMissing($price);
    }

    /** @test */
    public function search_by_customer_name_works_correctly()
    {
        $customerA = Customer::factory()->create(['name' => 'Customer Alpha']);
        $customerB = Customer::factory()->create(['name' => 'Customer Beta']);
        $priceA = Price::factory()->create(['customer_id' => $customerA->id]);
        $priceB = Price::factory()->create(['customer_id' => $customerB->id]);

        Livewire::test(Index::class)
            ->set('search', 'Alpha')
            ->assertSee($customerA->name)
            ->assertDontSee($customerB->name);
    }
}
