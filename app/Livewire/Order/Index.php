<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Order;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\ProductWarehouse;
use App\Models\Movement;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    protected $listeners = ['payment-registered' => '$refresh'];

    public $search = '';
    public $cambiarStatus = '';
    public $fechaInicio;
    public $fechaFin;

    public function render()
    {
        /** @disregard P1013 */
        $user = auth()->user();

        if ($user->hasRole('superadmin')) {
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
                })->whereBetween(DB::raw('DATE(created_at)'), [$this->fechaInicio ?? '2000-01-01', $this->fechaFin ?? date('Y-m-d')])
                ->orderBy('id', 'desc')
                ->paginate(10);
        } else {

            $orders = Order::with('customer', 'warehouse')
                ->where(function ($query) {
                    $query->where('status', 'like', '%' . $this->search . '%')
                        ->orWhere('order_type', 'like', '%' . $this->search . '%')
                        ->orWhereHas('customer', function ($query) {
                            $query->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('warehouse', function ($query) {
                            $query->where('name', 'like', '%' . $this->search . '%');
                        });                        ;
                })->whereBetween(DB::raw('DATE(created_at)'), [$this->fechaInicio ?? '2000-01-01', $this->fechaFin ?? date('Y-m-d')])
                ->where('user_id', Auth::id())
                ->orderBy('id', 'desc')
                ->paginate(10);
        }       
             

        return view('livewire.order.index', [
            'orders' => $orders,
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

        DB::transaction(function () use ($order) {
            // Revert stock changes
            foreach ($order->orderProducts as $orderProduct) {
                $productWarehouse = ProductWarehouse::firstOrNew([
                    'product_id' => $orderProduct->product_id,
                    'warehouse_id' => $order->warehouse_id,
                ]);

                if ($order->order_type === 'Entrada') {
                    $productWarehouse->stock -= $orderProduct->quantity;
                } else { // Salida
                    $productWarehouse->stock += $orderProduct->quantity;
                }
                $productWarehouse->save();

                if ($order->order_type === 'Entrada') {

                   $this->cambiarStatus = 'Salida';

                }else {

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
                } else { // Salida
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