<?php

namespace App\Livewire\Order;

use App\Models\Order;
use App\Models\Movement;
use App\Models\OrderProduct;
use App\Models\ProductWarehouse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProcessReturn extends Component
{
    public Order $order;
    public int $step = 1;
    public array $devoluciones = [];
    public array $summary = [];

    public function mount(Order $order)
    {
        $this->order = $order;
        // Inicializar el array de devoluciones con 0 para cada producto de la orden
        foreach ($this->order->orderProducts as $orderProduct) {
            $this->devoluciones[$orderProduct->id] = 0;
        }
    }

    public function goToStep2()
    {
        $this->validate([
            'devoluciones.*' => ['integer', 'min:0'],
        ]);
        
        $totalDevoluciones = array_sum($this->devoluciones);

        if ($totalDevoluciones <= 0) {
            session()->flash('error', 'Debes introducir una cantidad a devolver para al menos un producto.');
            return;
        }

        // Validar que no se devuelva más de lo comprado
        foreach ($this->devoluciones as $orderProductId => $cantidadADevolver) {
            if ($cantidadADevolver > 0) {
                $orderProduct = OrderProduct::find($orderProductId);
                if ($cantidadADevolver > $orderProduct->quantity) {
                    session()->flash('error', "No puedes devolver más unidades de '{$orderProduct->product->name}' de las que se compraron ({$orderProduct->quantity}).");
                    return;
                }
            }
        }
        
        $this->prepareSummary();
        $this->step = 2;
    }

    public function goToStep1()
    {
        $this->summary = [];
        $this->step = 1;
    }

    public function prepareSummary()
    {
        $this->summary = [];
        $totalCredito = 0;

        foreach ($this->devoluciones as $orderProductId => $cantidad) {
            if ($cantidad > 0) {
                $orderProduct = OrderProduct::find($orderProductId);
                $subtotal = $cantidad * $orderProduct->price;
                $totalCredito += $subtotal;
                $this->summary['items'][] = [
                    'name' => $orderProduct->product->name,
                    'quantity' => $cantidad,
                    'price' => $orderProduct->price,
                    'subtotal' => $subtotal,
                ];
            }
        }
        $this->summary['total_credito'] = $totalCredito;
    }


    public function confirmReturn()
    {
        if ($this->step !== 2) {
            return;
        }

        DB::transaction(function () {
            foreach ($this->devoluciones as $orderProductId => $cantidadADevolver) {
                if ($cantidadADevolver > 0) {
                    $orderProduct = OrderProduct::findOrFail($orderProductId);
                    
                    // 1. Crear movimiento de Devolucion para el inventario
                    Movement::create([
                        'product_id' => $orderProduct->product_id,
                        'warehouse_id' => $this->order->warehouse_id,
                        'order_id' => $this->order->id,
                        'type' => 'Devolucion',
                        'quantity' => $cantidadADevolver,
                        'date' => now(),
                    ]);

                    // 2. Actualizar stock en la bodega
                    ProductWarehouse::where('product_id', $orderProduct->product_id)
                                    ->where('warehouse_id', $this->order->warehouse_id)
                                    ->increment('stock', $cantidadADevolver);

                    // 3. Actualizar la cantidad en la orden original
                    $newQuantity = $orderProduct->quantity - $cantidadADevolver;
                    if ($newQuantity > 0) {
                        $orderProduct->update(['quantity' => $newQuantity]);
                    } else {
                        // Si se devuelven todos, se elimina el registro de la orden
                        $orderProduct->delete();
                    }
                }
            }

            // 4. Recalcular totales de la orden
            $this->order->refresh()->recalculateTotals();
            $this->order->save();

            // 5. Opcional: Ajustar el balance del cliente
            $customer = $this->order->customer;
            if ($customer) {
                // Asumiendo que el cliente tiene un campo 'credit_balance'
                if (isset($this->summary['total_credito'])) {
                     $customer->increment('credit_balance', $this->summary['total_credito']);
                }
            }
        });

        $this->step = 3;
    }

    public function render()
    {
        return view('livewire.order.process-return')
                ->extends('layouts.app');
    }
}