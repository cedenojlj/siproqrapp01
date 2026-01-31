# Plan de Implementación: Modificación de Órdenes

Este documento detalla el plan para implementar la funcionalidad de modificación de productos dentro de una orden existente, basado en la **Opción A: Modificar la Orden Existente**.

## 1. Estrategia General

La estrategia consiste en crear un nuevo componente de Livewire dedicado a la edición de los productos de una orden. Este componente permitirá al usuario seleccionar uno o más productos de una orden y eliminarlos. La lógica del backend se encargará de:
1.  Revertir el movimiento de inventario correspondiente.
2.  Actualizar el stock disponible.
3.  Eliminar el producto de la orden.
4.  Recalcular los totales de la orden.

Todo el proceso estará envuelto en una transacción de base de datos para garantizar la integridad de los datos.

## 2. Pasos de Implementación

### Paso 1: Crear el Nuevo Componente Livewire

Se creará un nuevo componente para manejar esta lógica de forma aislada.

- **Comando Artisan:**
  ```bash
  php artisan make:livewire Order\EditProducts
  ```
- **Archivos Generados:**
  - `app/Livewire/Order/EditProducts.php`
  - `resources/views/livewire/order/edit-products.blade.php`

### Paso 2: Definir la Ruta

Se añadirá una nueva ruta en `routes/web.php` para que se pueda acceder al componente a través de una URL, pasando el ID de la orden.

- **Ruta:**
  ```php
  use App\Livewire\Order\EditProducts;

  Route::get('/orders/{order}/edit-products', EditProducts::class)->name('orders.edit-products');
  ```

### Paso 3: Modificar la Interfaz de Usuario (Vista Blade)

En `resources/views/livewire/order/edit-products.blade.php`:

1.  **Estructura de la Vista:** La vista extenderá el layout principal de la aplicación y mostrará información clave de la orden (ID, cliente, fecha).
2.  **Lista de Productos:** Se iterará sobre los productos de la orden. Cada fila mostrará:
    - Un **checkbox** para seleccionar el producto a eliminar. El `wire:model` se vinculará a una propiedad `productosParaEliminar`.
    - Nombre del producto, cantidad y precio.
3.  **Botón de Acción:** Se incluirá un botón "Eliminar Seleccionados" que activará el método `eliminarProductosSeleccionados` en el componente. Se le añadirá una confirmación de Javascript para prevenir acciones accidentales.

### Paso 4: Lógica en el Componente Livewire

En `app/Livewire/Order/EditProducts.php`:

1.  **Propiedades Públicas:**
    - `public Order $order;`
    - `public array $productosParaEliminar = [];`
2.  **Método `mount(Order $order)`:** Recibirá la orden desde la ruta y la inicializará en el componente.
3.  **Método `render()`:** Renderizará la vista Blade correspondiente.
4.  **Método `eliminarProductosSeleccionados()`:**
    - **Validación:** Comprobará que se hayan seleccionado productos.
    - **Transacción de BD:** Usará `DB::transaction()` para envolver toda la lógica.
    - **Bucle de Eliminación:** Para cada producto seleccionado:
        a. Buscará el registro en la tabla pivote `order_products`.
        b. Creará un **movimiento de inventario inverso** (ej. `ENTRADA_AJUSTE`) para devolver el producto al stock.
        c. Actualizará el conteo de stock en `product_warehouses` usando `increment()`.
        d. Eliminará el registro de `order_products`.
    - **Recálculo de Totales:** Llamará a un método `recalculateTotals()` en el modelo `Order`.
    - **Refresco:** Limpiará la selección y recargará los datos de la orden para actualizar la vista.
    - **Notificación:** Enviará un evento al frontend para notificar al usuario del éxito de la operación.

### Paso 5: Lógica en el Modelo (Opcional pero recomendado)

En `app/Models/Order.php`:

1.  **Método `recalculateTotals()`:**
    - Este método encapsulará la lógica para recalcular el subtotal y el total de la orden basándose en los productos que aún contiene, asegurando que esta lógica sea reutilizable.

### Paso 6: Testing (Recomendado)

Se recomienda crear un test de Feature para validar el flujo completo, asegurando que el inventario y los totales de la orden se actualizan correctamente y que los registros correspondientes se crean/eliminan de la base de datos.
