<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'warehouse_id',
        'order_type',
        'total',
        'status',
        'payment_status',
        'monto_pagado',
        'date_expiration',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function paymentApplications()
    {
        return $this->hasMany(PaymentApplication::class);
    }

    public function getDeudaAttribute()
    {
        return $this->total - $this->monto_pagado;
    }

    protected static function booted()
    {
        static::updating(function ($order) {
            // Check if status is changing to 'Rechazada' under specific conditions
            if ($order->isDirty('status') && $order->status === 'Rechazada' && $order->order_type === 'Salida' && in_array($order->getOriginal('status'), ['Aprobada', 'Pendiente'])) {
                if ($order->monto_pagado > 0) {
                    self::reversePaymentsForOrder($order);
                }
            }
        });

        static::deleting(function ($order) {
            // Check conditions before deleting
            if ($order->order_type === 'Salida' && $order->monto_pagado > 0) {
                self::reversePaymentsForOrder($order);
            }
        });
    }

    public static function reversePaymentsForOrder(Order $order)
    {
        DB::transaction(function () use ($order) {
            $customer = $order->customer;
            $applications = $order->paymentApplications()->get();

            foreach ($applications as $application) {
                $montoRevertir = $application->monto_aplicado;

                // 1. Add the amount back to the customer's credit balance
                $customer->increment('credit_balance', $montoRevertir);

                // 2. Create a negative payment record for auditing
                Payment::create([
                    'customer_id' => $order->customer_id,
                    'monto' => -$montoRevertir,
                    'fecha_pago' => now(),
                    'metodo_pago' => 'Efectivo', // Default value, could be 'Sistema'
                    'notas' => "Reversión automática por evento en Orden #" . $order->id,
                ]);

                // 3. Delete the payment application record
                $application->delete();
            }

            // 4. Reset the order's paid amount and status
            $order->monto_pagado = 0;
            if ($order->total > 0) {
                $order->payment_status = 'pendiente';
            }
            // Note: For the 'updating' event, changes to the $order object here
            // will be automatically persisted. No need to call $order->save().
        });
    }
}