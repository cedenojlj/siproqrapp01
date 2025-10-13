<?php
namespace App\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentManager extends Component
{
    public $customer_id;
    public $monto_abono;
    public $fecha_pago;
    public $notas;
    public $metodo_pago;

    public $customers = [];
    public $selectedCustomer;

    protected $rules = [
        'customer_id' => 'required|exists:customers,id',
        'monto_abono' => 'required|numeric|min:0.01',
        'fecha_pago' => 'required|date',
        'metodo_pago' => 'required|in:Efectivo,Transferencia,Pago_Movil,Zelle,Divisa,Euro',
    ];

    public function mount()
    {
        $this->customers = Customer::all(['id', 'name']); // Optimizado para solo traer lo necesario
        $this->fecha_pago = now()->format('Y-m-d');
    }

    public function updatedCustomerId($id)
    {
        $this->selectedCustomer = Customer::with(['orders' => function ($query) {
            $query->whereIn('payment_status', ['pendiente', 'parcial'])->orderBy('created_at', 'asc');
        }])->find($id);
    }

    public function applyPayment()
    {
        $this->validate();

        DB::transaction(function () {
            $customer = Customer::find($this->customer_id);
            $montoTotalDisponible = $this->monto_abono + $customer->credit_balance;
            $montoRestante = $montoTotalDisponible;

            // 1. Registrar el pago principal
            $payment = Payment::create([
                'customer_id' => $this->customer_id,
                'monto' => $this->monto_abono,
                'fecha_pago' => $this->fecha_pago,
                'metodo_pago' => $this->metodo_pago,
                'notas' => $this->notas,
            ]);

            // 2. Obtener órdenes pendientes (más antiguas primero)
            $orders = $customer->orders()
                ->whereIn('payment_status', ['pendiente', 'parcial'])
                ->orderBy('created_at', 'asc')
                ->get();

            // 3. Iterar y aplicar el monto disponible
            foreach ($orders as $order) {
                if ($montoRestante <= 0) break;

                $deudaOrden = $order->getDeudaAttribute(); // Usamos el accesor
                $montoAAplicar = min($montoRestante, $deudaOrden);

                if ($montoAAplicar > 0) {
                    // Actualizar la orden
                    $order->monto_pagado += $montoAAplicar;
                    $order->payment_status = ($order->monto_pagado >= $order->total) ? 'pagado' : 'parcial';
                    $order->save();

                    // Registrar la aplicación del pago
                    $payment->applications()->create([
                        'order_id' => $order->id,
                        'monto_aplicado' => $montoAAplicar,
                    ]);

                    $montoRestante -= $montoAAplicar;
                }
            }

            // 4. Actualizar el saldo a favor del cliente
            $customer->credit_balance = $montoRestante;
            $customer->save();
        });

        // 5. Feedback y reseteo del formulario
        session()->flash('message', 'Pago aplicado exitosamente.');
        $this->reset(['customer_id', 'monto_abono', 'notas', 'selectedCustomer', 'metodo_pago']);
        $this->fecha_pago = now()->format('Y-m-d');
        
        // 6. Notificar a otros componentes que refresquen su data
        $this->dispatch('payment-registered');
    }

    public function render()
    {
        return view('livewire.customer.payment-manager');
    }
}