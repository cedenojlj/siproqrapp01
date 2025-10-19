<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Order;
use App\Models\Customer;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\ProductWarehouse;
use App\Models\Movement;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;



    public $search = '';
    public $cambiarStatus = '';
    public $fechaInicio;
    public $fechaFin;
    public $status_pago = '';
    public $status_general = '';
    public $order_type = '';
    public $customer_id = '';

    public function render()
    {
        /** @disregard P1013 */
        $user = auth()->user();

        $query = Order::with('customer', 'warehouse');

        if (!$user->hasRole('superadmin')) {
            $query->where('user_id', Auth::id());
        }

        $query->where(function ($q) {
            $q->where('id', 'like', '%' . $this->search . '%')
                ->orWhereHas('customer', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('warehouse', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                });
        });

        $query->when($this->status_pago, function ($q) {
            $q->where('payment_status', $this->status_pago);
        });

        $query->when($this->status_general, function ($q) {
            $q->where('status', $this->status_general);
        });

        $query->when($this->order_type, function ($q) {
            $q->where('order_type', $this->order_type);
        });

        $query->when($this->customer_id, function ($q) {
            $q->where('customer_id', $this->customer_id);
        });

        $query->whereBetween(DB::raw('DATE(created_at)'), [$this->fechaInicio ?? '2000-01-01', $this->fechaFin ?? date('Y-m-d')]);

        $orders = $query->orderBy('id', 'desc')->paginate(10);
        $customers = Customer::orderBy('name')->get();

        return view('livewire.order.index', [
            'orders' => $orders,
            'customers' => $customers,
        ]);
    }

    public function delete(Order $order)
    {

        //se va a anular la orden pero sin borrar el registro original para mantener el historial

        //verificar si la orden tiene status rechazada
        if ($order->status === 'Rechazada') {
            session()->flash('error', 'No se puede anular una orden ya rechazada.');
            return;
        }

        //verificar si la orden es de devolucion
        if ($order->order_type === 'Devolucion') {

            $order->update(['status' => 'Rechazada']); // Update status to 'Rechazada'

            session()->flash('message', 'Order cancelled successfully.');
            
            return;
        }

        

        DB::transaction(function () use ($order) {
            // Revert stock changes
            foreach ($order->orderProducts as $orderProduct) {
                $productWarehouse = ProductWarehouse::firstOrNew([
                    'product_id' => $orderProduct->product_id,
                    'warehouse_id' => $order->warehouse_id,
                ]);

                if ($order->order_type === 'Entrada') {

                    $productWarehouse->stock -= $orderProduct->quantity;

                } elseif ($order->order_type === 'Salida' or $order->order_type === 'Interna') { // Salida

                    $productWarehouse->stock += $orderProduct->quantity;
                }

                $productWarehouse->save();

                if ($order->order_type === 'Entrada') {

                    $this->cambiarStatus = 'Salida';

                } elseif ($order->order_type === 'Salida' or $order->order_type === 'Interna') {

                    $this->cambiarStatus = 'Entrada';
                }

                // Create movement record
                Movement::create([
                    'product_id' => $orderProduct->product_id,
                    'warehouse_id' => $order->warehouse_id,
                    'quantity' => $orderProduct->quantity,
                    'type' => $this->cambiarStatus, // 'Entrada' or 'Salida'
                    'order_id' => $order->id,
                    'date' => date('Y-m-d H:i:s'),
                ]);
            }

            // Delete associated movements
            // Movement::where('order_id', $order->id)->delete();

            // Delete order products
            // $order->orderProducts()->delete();

            // Delete the order itself
            // $order->delete();
        });

        $this->cambiarStatus = '';
        $order->update(['status' => 'Rechazada']); // Update status to 'Rechazada'

        session()->flash('message', 'Order cancelled successfully.');
    }

    public function borrar(Order $order)
    {

        //se va a anular la orden pero sin borrar el registro original para mantener el historial

        DB::transaction(function () use ($order) {
            // Revert stock changes
            foreach ($order->orderProducts as $orderProduct) {
                $productWarehouse = ProductWarehouse::firstOrNew([
                    'product_id' => $orderProduct->product_id,
                    'warehouse_id' => $order->warehouse_id,
                ]);

                if ($order->order_type === 'Entrada') {

                    $productWarehouse->stock -= $orderProduct->quantity;

                } elseif ($order->order_type === 'Salida' or $order->order_type === 'Interna') { // Salida

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
