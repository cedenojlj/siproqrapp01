<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Warehouse;
use App\Models\Classification;
use App\Models\Price;
use App\Models\ProductWarehouse;
use App\Models\Movement;
use App\Livewire\Order\Create;
use App\Livewire\Order\Edit;
use App\Livewire\Order\Index;
use App\Livewire\Order\Show;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;
    private User $regularUser;
    private Customer $customer;
    private Product $product;
    private Warehouse $warehouse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PermisosSeeder::class);

        $this->superadmin = User::where('email', 'superadmin@example.com')->first();
        $this->regularUser = User::where('email', 'usuario@example.com')->first();
        
        $this->customer = Customer::factory()->create();
        $this->warehouse = Warehouse::factory()->create();
        $classification = Classification::factory()->create(['unit_type' => 'Cantidad']);

        $this->product = Product::factory()->create(['classification_id' => $classification->id]);
        ProductWarehouse::create(['product_id' => $this->product->id, 'warehouse_id' => $this->warehouse->id, 'stock' => 100]);

        Price::factory()->create([
            'product_id' => $this->product->id,
            'customer_id' => $this->customer->id,
            'price_quantity' => 10.00,
        ]);
    }

    /** @test */
    public function the_order_pages_can_be_rendered()
    {
        $this->actingAs($this->regularUser);
        $this->get(route('orders.index'))->assertStatus(200);
        $this->get(route('orders.create'))->assertStatus(200);

        $order = $this->createOrderForUser($this->regularUser);

        $this->get(route('orders.edit', $order))->assertStatus(200);
        $this->get(route('orders.show', $order))->assertStatus(200);
    }

    /** @test */
    public function it_can_create_an_entry_order()
    {
        $this->actingAs($this->regularUser);

        Livewire::test(Create::class)
            ->set('customer_id', $this->customer->id)
            ->set('warehouse_id', $this->warehouse->id)
            ->set('order_type', 'Entrada')
            ->set('products.0.product_id', $this->product->id)
            ->set('products.0.quantity', 10)
            ->call('saveOrder')
            ->assertRedirect(route('orders.index'));

        $this->assertDatabaseHas('orders', ['order_type' => 'Entrada']);
        $this->assertDatabaseHas('product_warehouses', ['product_id' => $this->product->id, 'stock' => 110]);
        $this->assertDatabaseHas('movements', ['product_id' => $this->product->id, 'type' => 'Entrada', 'quantity' => 10]);
    }

    /** @test */
    public function it_can_create_an_exit_order()
    {
        $this->actingAs($this->regularUser);

        Livewire::test(Create::class)
            ->set('customer_id', $this->customer->id)
            ->set('warehouse_id', $this->warehouse->id)
            ->set('order_type', 'Salida')
            ->set('products.0.product_id', $this->product->id)
            ->set('products.0.quantity', 10)
            ->call('saveOrder')
            ->assertRedirect(route('orders.index'));

        $this->assertDatabaseHas('orders', ['order_type' => 'Salida']);
        $this->assertDatabaseHas('product_warehouses', ['product_id' => $this->product->id, 'stock' => 90]);
        $this->assertDatabaseHas('movements', ['product_id' => $this->product->id, 'type' => 'Salida', 'quantity' => 10]);
    }

    /** @test */
    public function regular_user_sees_only_their_orders()
    {
        $this->actingAs($this->regularUser);
        $this->createOrderForUser($this->regularUser, ['status' => 'Aprobada']);
        $this->createOrderForUser($this->superadmin, ['status' => 'Pendiente']);

        Livewire::test(Index::class)
            ->assertSee('Aprobada')
            ->assertDontSee('Pendiente');
    }

    /** @test */
    public function superadmin_sees_all_orders()
    {
        $this->actingAs($this->superadmin);
        $this->createOrderForUser($this->regularUser, ['status' => 'Aprobada']);
        $this->createOrderForUser($this->superadmin, ['status' => 'Pendiente']);

        Livewire::test(Index::class)
            ->assertSee('Aprobada')
            ->assertSee('Pendiente');
    }

    /** @test */
    public function it_can_update_order_status()
    {
        $this->actingAs($this->superadmin);
        $order = $this->createOrderForUser($this->regularUser);

        Livewire::test(Edit::class, ['order' => $order])
            ->set('status', 'Rechazada')
            ->call('updateOrder')
            ->assertRedirect(route('orders.index'));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'Rechazada']);
    }

    /** @test */
    public function it_can_cancel_an_order()
    {
        $this->actingAs($this->superadmin);
        $order = $this->createOrderForUser($this->regularUser, ['order_type' => 'Salida']);
        $order->orderProducts()->create(['product_id' => $this->product->id, 'quantity' => 10, 'price' => 10, 'subtotal' => 100]);
        ProductWarehouse::where('product_id', $this->product->id)->update(['stock' => 90]);

        Livewire::test(Index::class)
            ->call('delete', $order->id);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'Rechazada']);
        $this->assertDatabaseHas('product_warehouses', ['product_id' => $this->product->id, 'stock' => 100]);
    }

    /** @test */
    public function it_can_hard_delete_an_order()
    {
        $this->actingAs($this->superadmin);
        $order = $this->createOrderForUser($this->regularUser);
        $orderProduct = $order->orderProducts()->create(['product_id' => $this->product->id, 'quantity' => 1, 'price' => 10, 'subtotal' => 10]);

        Livewire::test(Index::class)
            ->call('borrar', $order->id);

        $this->assertModelMissing($order);
        $this->assertModelMissing($orderProduct);
    }

    private function createOrderForUser(User $user, array $options = []): Order
    {
        return Order::create(array_merge([
            'customer_id' => $this->customer->id,
            'user_id' => $user->id,
            'warehouse_id' => $this->warehouse->id,
            'order_type' => 'Entrada',
            'status' => 'Aprobada',
            'total' => 100,
        ], $options));
    }
}
