# Plan de Implementación: Sistema de Control de Pagos y Abonos

**Filosofía:** Crear un sistema de pagos centralizado y automatizado usando Livewire. Cuando un cliente realiza un abono, el sistema debe aplicarlo inteligentemente a las órdenes pendientes de pago, desde la más antigua a la más reciente, manejando pagos completos, parciales y saldos a favor.

---

### **Fase 1: Base de Datos y Modelos (El Fundamento)**

Esta fase es la más crítica. Un buen diseño de base de datos previene problemas a futuro.

**1. Migraciones de Base de Datos:**

*   **Modificar `orders`:** Añadimos un estado de pago y el monto que ya ha sido cubierto.
    *   `php artisan make:migration add_payment_fields_to_orders_table`
*   **Modificar `customers`:** Añadimos una columna para guardar el saldo a favor (crédito).
    *   `php artisan make:migration add_credit_balance_to_customers_table`
*   **Crear `payments`:** Una tabla para registrar cada transacción de pago que ingresa.
    *   `php artisan make:migration create_payments_table`
*   **Crear `payment_applications`:** Una tabla pivote que detalla exactamente cómo se distribuyó cada centavo de un pago entre las órdenes.
    *   `php artisan make:migration create_payment_applications_table`

**2. Código de las Migraciones:**

```php
// Migración para `orders`
Schema::table('orders', function (Blueprint $table) {
    $table->string('payment_status')->default('pendiente')->after('id'); // pendiente, pago_parcial, pagado
    $table->decimal('monto_pagado', 10, 2)->default(0)->after('total');
});

// Migración para `customers`
Schema::table('customers', function (Blueprint $table) {
    $table->decimal('credit_balance', 10, 2)->default(0)->after('email'); // Saldo a favor
});

// Migración para `payments`
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained()->onDelete('cascade');
    $table->decimal('monto', 10, 2);
    $table->date('fecha_pago');
    $table->enum('metodo_pago',['Efectivo','Transferencia','Pago_Movil','Zelle','Divisa','Euro']);
    $table->text('notas')->nullable();
    $table->timestamps();
});

// Migración para `payment_applications`
Schema::create('payment_applications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('payment_id')->constrained()->onDelete('cascade');
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->decimal('monto_aplicado', 10, 2);
    $table->timestamps();
});
```

**3. Relaciones en los Modelos (Eloquent):**

Es vital definir cómo se conectan los modelos para que Laravel trabaje por nosotros.

```php
// app/Models/Customer.php
public function orders() { return $this->hasMany(Order::class); }
public function payments() { return $this->hasMany(Payment::class); }

// app/Models/Order.php
public function customer() { return $this->belongsTo(Customer::class); }
public function paymentApplications() { return $this->hasMany(PaymentApplication::class); }
public function getDeudaAttribute() { return $this->total - $this->monto_pagado; } // Accesor útil

// app/Models/Payment.php
public function customer() { return $this->belongsTo(Customer::class); }
public function applications() { return $this->hasMany(PaymentApplication::class); }

// app/Models/PaymentApplication.php
public function payment() { return $this->belongsTo(Payment::class); }
public function order() { return $this->belongsTo(Order::class); }
```

---

### **Fase 2: Lógica de Negocio con Livewire**

Aquí reside la inteligencia del sistema. Un único componente manejará todo el proceso.

**1. Crear el Componente Livewire:**

```bash
php artisan make:livewire Customer/PaymentManager
```

**2. Código del Componente (`app/Livewire/Customer/PaymentManager.php`):**

Este componente será el cerebro de la operación.

```php
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

    public $customers = [];
    public $selectedCustomer;

    protected $rules = [
        'customer_id' => 'required|exists:customers,id',
        'monto_abono' => 'required|numeric|min:0.01',
        'fecha_pago' => 'required|date',
    ];

    public function mount()
    {
        $this->customers = Customer::all(['id', 'name']); // Optimizado para solo traer lo necesario
        $this->fecha_pago = now()->format('Y-m-d');
    }

    public function updatedCustomerId($id)
    {
        $this->selectedCustomer = Customer::with(['orders' => function ($query) {
            $query->whereIn('payment_status', ['pendiente', 'pago_parcial'])->orderBy('created_at', 'asc');
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
                'notas' => $this->notas,
            ]);

            // 2. Obtener órdenes pendientes (más antiguas primero)
            $orders = $customer->orders()
                ->whereIn('payment_status', ['pendiente', 'pago_parcial'])
                ->orderBy('created_at', 'asc')
                ->get();

            // 3. Iterar y aplicar el monto disponible
            foreach ($orders as $order) {
                if ($montoRestante <= 0) break;

                $deudaOrden = $order->deuda; // Usamos el accesor
                $montoAAplicar = min($montoRestante, $deudaOrden);

                if ($montoAAplicar > 0) {
                    // Actualizar la orden
                    $order->monto_pagado += $montoAAplicar;
                    $order->payment_status = ($order->monto_pagado >= $order->total) ? 'pagado' : 'pago_parcial';
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
        $this->reset(['customer_id', 'monto_abono', 'notas', 'selectedCustomer']);
        $this->fecha_pago = now()->format('Y-m-d');
        
        // 6. Notificar a otros componentes que refresquen su data
        $this->dispatch('payment-registered');
    }

    public function render()
    {
        return view('livewire.customer.payment-manager');
    }
}
```

---

### **Fase 3: Interfaz de Usuario (UI) y Rutas**

Una buena lógica merece una interfaz clara y funcional.

**1. Ruta para la Vista:**

Añade esto en `routes/web.php` para poder acceder a la página de gestión de pagos.

```php
use App\Livewire\Customer\PaymentManager;

Route::get('/payments/manage', PaymentManager::class)->name('payments.manage');
```

**2. Vista del Componente (`resources/views/livewire/customer/payment-manager.blade.php`):**

Esta vista será la interfaz para el usuario. Usaremos un diseño limpio y con feedback en tiempo real.

```html
<div>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="applyPayment">
        <div class="card">
            <div class="card-header">
                <h4>Registrar Abono de Cliente</h4>
            </div>
            <div class="card-body">
                <!-- Selector de Cliente (se puede mejorar con un buscador) -->
                <div class="form-group">
                    <label for="customer">Cliente</label>
                    <select id="customer" wire:model.live="customer_id" class="form-control">
                        <option value="">-- Seleccione un cliente --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    @error('customer_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                @if($selectedCustomer)
                    <div class="mt-3">
                        <p><strong>Saldo a favor actual:</strong> ${{ number_format($selectedCustomer->credit_balance, 2) }}</p>
                        <h5>Órdenes Pendientes:</h5>
                        @if($selectedCustomer->orders->isNotEmpty())
                            <ul class="list-group">
                                @foreach($selectedCustomer->orders as $order)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Orden #{{ $order->id }} ({{ $order->created_at->format('d/m/Y') }})
                                        <span class="badge badge-warning">Deuda: ${{ number_format($order->deuda, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>Este cliente no tiene órdenes pendientes.</p>
                        @endif
                    </div>
                @endif

                <hr>

                <!-- Campos del Abono -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="monto_abono">Monto del Abono</label>
                            <input type="number" step="0.01" id="monto_abono" wire:model="monto_abono" class="form-control">
                            @error('monto_abono') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_pago">Fecha del Pago</label>
                            <input type="date" id="fecha_pago" wire:model="fecha_pago" class="form-control">
                            @error('fecha_pago') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label for="notas">Notas (Opcional)</label>
                    <textarea id="notas" wire:model="notas" class="form-control"></textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="applyPayment">Aplicar Pago</span>
                    <span wire:loading wire:target="applyPayment">Procesando...</span>
                </button>
            </div>
        </div>
    </form>
</div>
```

---

### **Fase 4: Actualización de Vistas Existentes**

Para que el sistema se sienta cohesivo, otros componentes deben reaccionar a los pagos.

1.  **Componente de Lista de Órdenes (`Order/Index.php`):**
    *   Este componente debe escuchar el evento `payment-registered` para refrescarse automáticamente.
    *   Añade esta línea en el componente:
        ```php
        protected $listeners = ['payment-registered' => '$refresh'];
        ```
    *   Esto hace que la lista de órdenes se actualice en tiempo real sin que el usuario tenga que recargar la página.

2.  **Vistas de Órdenes:**
    *   Modifica la vista de la lista de órdenes para mostrar el `payment_status` con etiquetas de colores (ej. "Pagado" en verde, "Pago Parcial" en amarillo) y mostrar el `monto_pagado` vs el `total`.

---

### **Fase 5: Pruebas (Testing)**

Las pruebas son la red de seguridad. Con Livewire, podemos simular la interacción del usuario de forma muy eficiente.

*   Crea un test para `Customer/PaymentManager`.
*   **Escenario 1: Pago completo.** Simula un pago que cubre exactamente una orden. Verifica que el estado de la orden cambie a `pagado`.
*   **Escenario 2: Pago parcial.** Simula un pago menor a la deuda. Verifica que el estado sea `pago_parcial` y el `monto_pagado` sea correcto.
*   **Escenario 3: Pago múltiple.** Simula un pago que cubre una orden y parte de la siguiente. Verifica que ambas órdenes se actualicen correctamente.
*   **Escenario 4: Saldo a favor.** Simula un pago que excede todas las deudas. Verifica que el `credit_balance` del cliente se actualice.
*   **Escenario 5: Uso de saldo a favor.** Realiza un pago, genera saldo a favor, y luego verifica que un nuevo abono utilice primero ese saldo.