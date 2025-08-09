<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Order;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\ProductWarehouse;
use App\Models\Movement;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $orders = Order::with('customer', 'warehouse')
            ->where(function ($query) {
                $query->where('status', 'like', '%' . $this->search . '%')
                      ->orWhere('order_type', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customer', function ($query) {
                          $query->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('warehouse', function ($query) {
                          $query->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->paginate(10);

        return view('livewire.order.index', [
            'orders' => $orders,
        ]);
    }

    public function delete(Order $order)
    {
        DB::transaction(function () use ($order) {
            // Revert stock changes
            foreach ($order->orderProducts as $orderProduct) {
                $productWarehouse = ProductWarehouse::firstOrNew([
                    'product_id' => $orderProduct->product_id,
                    'warehouse_id' => $order->warehouse_id,
                ]);

                if ($order->order_type === 'entry') {
                    $productWarehouse->stock -= $orderProduct->quantity;
                } else { // exit
                    $productWarehouse->stock += $orderProduct->quantity;
                }
                $productWarehouse->save();
            }

            // Delete associated movements
            Movement::where('order_id', $order->id)->delete();

            // Delete order products
            $order->orderProducts()->delete();

            // Delete the order itself
            $order->delete();
        });

        session()->flash('message', 'Order deleted successfully.');
    }
}