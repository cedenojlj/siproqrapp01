<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Movement;
use App\Models\Order;
use App\Models\Customer;
use App\Livewire\Report\HistoricalMovements;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Carbon\Carbon;

class MovementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $productA;
    private Product $productB;
    private Warehouse $warehouseA;
    private Warehouse $warehouseB;
    private Order $order;
    private Movement $movementA;
    private Movement $movementB;
    private Movement $movementC;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->productA = Product::factory()->create(['name' => 'Product A']);
        $this->productB = Product::factory()->create(['name' => 'Product B']);
        $this->warehouseA = Warehouse::factory()->create(['name' => 'Warehouse A']);
        $this->warehouseB = Warehouse::factory()->create(['name' => 'Warehouse B']);
        $customer = Customer::factory()->create();

        $this->order = Order::create([
            'customer_id' => $customer->id,
            'user_id' => $this->user->id,
            'warehouse_id' => $this->warehouseA->id,
            'order_type' => 'Entrada',
            'total' => 100.00,
            'status' => 'Aprobada',
        ]);

        // Create some movements
        $this->movementA = Movement::create([
            'product_id' => $this->productA->id,
            'warehouse_id' => $this->warehouseA->id,
            'order_id' => $this->order->id,
            'quantity' => 10,
            'type' => 'Entrada',
            'date' => '2025-08-20',
            'created_at' => '2025-08-20 00:00:00'
        ]);

        $this->movementB = Movement::create([
            'product_id' => $this->productB->id,
            'warehouse_id' => $this->warehouseA->id,
            'order_id' => $this->order->id,
            'quantity' => 5,
            'type' => 'Salida',
            'date' => '2025-08-25',
            'created_at' => '2025-08-25 00:00:00'
        ]);

        $this->movementC = Movement::create([
            'product_id' => $this->productA->id,
            'warehouse_id' => $this->warehouseB->id,
            'order_id' => $this->order->id,
            'quantity' => 20,
            'type' => 'Entrada',
            'date' => '2025-08-30',
            'created_at' => '2025-08-30 00:00:00'
        ]);
    }

    /** @test */
    public function the_historical_movements_report_page_can_be_rendered()
    {
        $this->get(route('reports.historical-movements'))->assertStatus(200);
    }

    /** @test */
    public function it_shows_all_movements_initially()
    {
        $component = Livewire::test(HistoricalMovements::class);
        $this->assertCount(3, $component->viewData('movements'));
    }

    /** @test */
    public function it_can_filter_by_product()
    {
        $component = Livewire::test(HistoricalMovements::class)
            ->set('productId', $this->productB->id);

        $movements = $component->viewData('movements');

        $this->assertCount(1, $movements);
        $this->assertEquals($this->productB->id, $movements->first()->product_id);
    }

    /** @test */
    public function it_can_filter_by_warehouse()
    {
        $component = Livewire::test(HistoricalMovements::class)
            ->set('warehouseId', $this->warehouseB->id);
        
        $movements = $component->viewData('movements');

        $this->assertCount(1, $movements);
        $this->assertEquals($this->warehouseB->id, $movements->first()->warehouse_id);
    }

    /** @test */
    public function it_can_filter_by_movement_type()
    {
        $component = Livewire::test(HistoricalMovements::class)
            ->set('movementType', 'Salida');

        $movements = $component->viewData('movements');

        $this->assertCount(1, $movements);
        $this->assertEquals('Salida', $movements->first()->type);
    }

    /** @test */
    public function it_can_filter_by_date_range()
    {
        $component = Livewire::test(HistoricalMovements::class)
            ->set('startDate', '2025-08-24')
            ->set('endDate', '2025-08-26');

        $movements = $component->viewData('movements');

        $this->assertCount(1, $movements);
        $this->assertEquals($this->movementB->id, $movements->first()->id);
    }

    /** @test */
    public function it_can_filter_by_a_combination_of_filters()
    {
        // Create a specific movement to test combination
        $specificMovement = Movement::create([
            'product_id' => $this->productB->id,
            'warehouse_id' => $this->warehouseB->id,
            'order_id' => $this->order->id,
            'quantity' => 50,
            'type' => 'Entrada',
            'date' => '2025-08-28',
            'created_at' => '2025-08-28 00:00:00'
        ]);

        $component = Livewire::test(HistoricalMovements::class)
            ->set('productId', $this->productB->id)
            ->set('warehouseId', $this->warehouseB->id)
            ->set('movementType', 'Entrada');

        $movements = $component->viewData('movements');

        $this->assertCount(1, $movements);
        $this->assertEquals($specificMovement->id, $movements->first()->id);
    }

    /** @test */
    public function it_can_generate_a_pdf_report()
    {
        $response = Livewire::test(HistoricalMovements::class)->call('generatePdf');

        $response->assertFileDownloaded('historical_movements_report.pdf');
    }
}
