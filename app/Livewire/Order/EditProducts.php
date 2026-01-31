<?php

namespace App\Livewire\Order;

use App\Models\Order;
use App\Models\Movement;
use App\Models\OrderProduct;
use App\Models\ProductWarehouse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EditProducts extends Component
{
    public Order $order;
    public array $productosParaEliminar = [];

    public function mount(Order $order)
    {
        $this->order = $order;
    }

    public function eliminarProductosSeleccionados()
    {
        // 1. Validación
        if (empty($this->productosParaEliminar)) {
            // Reemplaza con tu sistema de notificaciones preferido
            session()->flash('message', 'No has seleccionado ningún producto para eliminar.');
            return;
        }

        // 2. Usar una transacción para garantizar la atomicidad
        DB::transaction(function () {
            foreach ($this->productosParaEliminar as $orderProductId) {
                // a. Encontrar el registro en la tabla pivote
                $orderProduct = OrderProduct::findOrFail($orderProductId);
                
                $product = $orderProduct->product;
                // Asumiendo que la orden tiene una bodega de origen. 
                // Si no, busca la bodega del primer movimiento de salida de este producto para esta orden.
                $warehouseId = $this->order->warehouse_id; 
                $quantity = $orderProduct->quantity;

                // b. Revertir el movimiento de inventario: Crear un movimiento de Devolucion
                Movement::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouseId,
                    'order_id' => $this->order->id,
                    'type' => 'Devolucion', // Ajustado al ENUM de la base de datos
                    'quantity' => (int)$quantity, // Asegurar que es un entero
                    'date' => now(), // Campo 'date' requerido
                ]);

                // c. Actualizar el stock en la tabla product_warehouses
                $stock = ProductWarehouse::where('product_id', $product->id)
                                         ->where('warehouse_id', $warehouseId)
                                         ->first();
                if ($stock) {
                    $stock->increment('stock', $quantity);
                } else {
                    // Si por alguna razón no existía el registro, lo crea.
                    ProductWarehouse::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouseId,
                        'stock' => $quantity,
                    ]);
                }

                // d. Eliminar el producto de la orden
                $orderProduct->delete();
            }

            // 3. Recalcular los totales de la orden
            if (method_exists($this->order, 'recalculateTotals')) {
                $this->order->recalculateTotals(); 
                $this->order->save();
            }
        });

        // 4. Limpiar la selección y refrescar los datos para la vista
        $this->productosParaEliminar = [];
        $this->order->refresh(); // Recarga la relación de productos

        session()->flash('success', 'Productos eliminados y stock actualizado correctamente.');
    }

    public function render()
    {
        return view('livewire.order.edit-products');
    }
}