<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();


        // create permissions
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'read users']);
        Permission::create(['name' => 'update users']);
        Permission::create(['name' => 'delete users']);

        Permission::create(['name' => 'create products']);
        Permission::create(['name' => 'read products']);
        Permission::create(['name' => 'update products']);
        Permission::create(['name' => 'delete products']);

        Permission::create(['name' => 'create customers']);
        Permission::create(['name' => 'read customers']);
        Permission::create(['name' => 'update customers']);
        Permission::create(['name' => 'delete customers']);

        Permission::create(['name' => 'create warehouses']);
        Permission::create(['name' => 'read warehouses']);
        Permission::create(['name' => 'update warehouses']);
        Permission::create(['name' => 'delete warehouses']);

        Permission::create(['name' => 'create petitions']);
        Permission::create(['name' => 'read petitions']);
        Permission::create(['name' => 'update petitions']);
        Permission::create(['name' => 'delete petitions']);

        Permission::create(['name' => 'create orders']);
        Permission::create(['name' => 'read orders']);
        Permission::create(['name' => 'update orders']);
        Permission::create(['name' => 'delete orders']);

        Permission::create(['name' => 'create precios']);
        Permission::create(['name' => 'read precios']);
        Permission::create(['name' => 'update precios']);
        Permission::create(['name' => 'delete precios']);       
        
        Permission::create(['name' => 'ver reportes']);
        Permission::create(['name' => 'ver clasificaciones']);
        Permission::create(['name' => 'ver roles']);
        Permission::create(['name' => 'ver invAlmacen']);
        Permission::create(['name' => 'ver movHistoricos']);
        Permission::create(['name' => 'ver movDetalles']);
        Permission::create(['name' => 'ver invClasificacion']);
        Permission::create(['name' => 'ver invClasificacionDetalle']);

        Permission::create(['name' => 'ver codigosQR']);
        
        Permission::create(['name' => 'ver abonos']);


        // create roles

        $rol4 = Role::create(['name' => 'proveedor']);

        $rol4->givePermissionTo([
            'create products',
            'ver codigosQR',            
        ]);


        $rol1 = Role::create(['name' => 'usuario']);

        $rol1->givePermissionTo([
            'create petitions',
            'read petitions',
            'update petitions',
            'delete petitions',
        ]);

        $rol2 = Role::create(['name' => 'almacenista']);
        
        /* $rol2->givePermissionTo([
            'create products',
            'read products',
            'update products',
            'delete products',
        ]); */

        $rol2->givePermissionTo([
            'create petitions',
            'read petitions',
            'update petitions',
            'delete petitions',            
        ]);

        $rol2->givePermissionTo([
            'create orders',
            'read orders',            
        ]);

        $rol2->givePermissionTo([
            'ver reportes',
            'read customers',
            // 'read warehouses',
        ]);

        $rol3 = Role::create(['name' => 'superadmin']); // this role gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $user = \App\Models\User::factory()->create([
            'name' => 'usuario',
            'email' => 'usuario@example.com',
        ]);
        $user->assignRole($rol1);

        $user = \App\Models\User::factory()->create([
            'name' => 'almacenista',
            'email' => 'almacenista@example.com',
        ]);
        $user->assignRole($rol2);

        $user = \App\Models\User::factory()->create([
            'name' => 'superadmin',
            'email' => 'superadmin@example.com',
        ]);
        $user->assignRole($rol3);























    }
}
