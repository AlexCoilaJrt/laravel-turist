<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $admin = Role::create(['name' => 'Admin']);
        $emprendedor = Role::create(['name' => 'Emprendedor']);
        $usuario = Role::create(['name' => 'Usuario']);

        // Permisos de admin
        Permission::create(['name' => 'gestionar usuarios'])->assignRole($admin);
        Permission::create(['name' => 'aprobar servicios'])->assignRole($admin);
        Permission::create(['name' => 'ver reportes'])->assignRole($admin);

        // Permisos de emprendedor
        Permission::create(['name' => 'crear servicios'])->assignRole($emprendedor);
        Permission::create(['name' => 'editar servicios'])->assignRole($emprendedor);
        Permission::create(['name' => 'eliminar servicios'])->assignRole($emprendedor);

        // Permisos de usuario
        Permission::create(['name' => 'reservar servicios'])->assignRole($usuario);
        Permission::create(['name' => 'calificar servicios'])->assignRole($usuario);
    }
}
