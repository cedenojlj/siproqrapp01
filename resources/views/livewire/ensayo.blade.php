<div>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Crear Nuevo Pedido por Clasificación</h1>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form wire:submit.prevent="save">            

            <div class="mb-4">  
                <label for="warehouse" class="block text-gray-700 text-sm font-bold mb-2">Almacén:</label>
                <select id="warehouse" wire:model.live="warehouseId" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">-- Seleccione un almacén --</option>
                    @foreach($allWarehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
                @error('warehouseId') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
            </div>

            <div class="overflow-x-auto bg-white rounded-lg shadow-md">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-2  border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Código</th>
                            <th class="px-5 py-2  border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Medida</th>
                            <th class="px-5 py-2  border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Descripción</th>
                            <th class="px-5 py-2  border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock Disponible</th>
                            <th class="px-5 py-2  border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Peso</th>
                            <th class="px-5 py-2  border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cantidad a Pedir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classifications as $classification)
                            <tr>
                                <td class="px-5 py-1 border-b border-gray-200 bg-white text-sm">{{ $classification->code }}</td>
                                <td class="px-5 py-1 border-b border-gray-200 bg-white text-sm">{{ $classification->size }}</td>
                                <td class="px-5 py-1 border-b border-gray-200 bg-white text-sm">{{ $classification->description }}</td>
                                <td class="px-5 py-1 border-b border-gray-200 bg-white text-sm">{{ $classification->total_stock }}</td>
                                <td class="px-5 py-1 border-b border-gray-200 bg-white text-sm">{{ $classification->total_gn }}</td>
                                <td class="px-5 py-1 border-b border-gray-200 bg-white text-sm">
                                    <input type="number" wire:model.defer="items.{{ $classification->id }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="0">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10">No hay productos con stock disponible.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                <button type="submit" class="btn btn-primary mt-4">
                    Crear Pedido
                </button>
            </div>
        </form>
    </div>
</div>
