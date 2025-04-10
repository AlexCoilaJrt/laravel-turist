<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos para Paquetes Turísticos
        Permission::create(['name' => 'ver_paquetes', 'guard_name' => 'api']);
        Permission::create(['name' => 'crear_editar_paquetes', 'guard_name' => 'api']);
        Permission::create(['name' => 'aprobar_paquetes', 'guard_name' => 'api']);
        Permission::create(['name' => 'reservar_paquete', 'guard_name' => 'api']);

        // Crear permisos para Artesanías y Comidas
        Permission::create(['name' => 'ver_catalogo', 'guard_name' => 'api']);
        Permission::create(['name' => 'crear_editar_productos', 'guard_name' => 'api']);
        Permission::create(['name' => 'comprar_productos', 'guard_name' => 'api']);

        // Crear permisos para Usuarios
        Permission::create(['name' => 'ver_usuarios', 'guard_name' => 'api']);
        Permission::create(['name' => 'crear_editar_usuarios', 'guard_name' => 'api']);
        Permission::create(['name' => 'eliminar_usuarios', 'guard_name' => 'api']);

        // Crear permisos para Roles y Empresas
        Permission::create(['name' => 'gestionar_roles_permisos', 'guard_name' => 'api']);
        Permission::create(['name' => 'ver_empresas', 'guard_name' => 'api']);
        Permission::create(['name' => 'crear_editar_empresas', 'guard_name' => 'api']);
    }
}
