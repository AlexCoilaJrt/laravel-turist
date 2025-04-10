<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos
        Permission::create(['name' => 'crear_usuarios']);
        Permission::create(['name' => 'editar_usuarios']);
        Permission::create(['name' => 'eliminar_usuarios']);
        Permission::create(['name' => 'crear_roles']);
        Permission::create(['name' => 'editar_roles']);
        Permission::create(['name' => 'eliminar_roles']);
        Permission::create(['name' => 'gestionar_permisos']);
        Permission::create(['name' => 'configurar_sistema']);
        Permission::create(['name' => 'ver_todo']);
        Permission::create(['name' => 'crear_paquetes_turisticos']);
        Permission::create(['name' => 'editar_paquetes_turisticos']);
        Permission::create(['name' => 'eliminar_paquetes_turisticos']);
        Permission::create(['name' => 'ver_mis_datos']);
        Permission::create(['name' => 'gestionar_productos']);
        Permission::create(['name' => 'gestionar_servicios']);
        Permission::create(['name' => 'realizar_reservas']);
        Permission::create(['name' => 'ver_productos']);

        // Crear roles
        $superAdmin = Role::create(['name' => 'SuperAdmin']);
        $admin = Role::create(['name' => 'Admin']);
        $emprendedor = Role::create(['name' => 'Emprendedor']);
        $usuario = Role::create(['name' => 'Usuario']);
        $invitado = Role::create(['name' => 'Invitado']);

        // Asignar permisos a SuperAdmin
        $superAdmin->givePermissionTo(Permission::all());

        // Asignar permisos a Admin
        $admin->givePermissionTo([
            'crear_usuarios',
            'editar_usuarios',
            'eliminar_usuarios',
            'gestionar_emprendedores',
            'ver_todo',
        ]);

        // Asignar permisos a Emprendedor
        $emprendedor->givePermissionTo([
            'crear_paquetes_turisticos',
            'editar_paquetes_turisticos',
            'eliminar_paquetes_turisticos',
            'gestionar_productos',
            'gestionar_servicios',
            'ver_mis_datos'
        ]);

        // Asignar permisos a Usuario
        $usuario->givePermissionTo([
            'ver_paquetes_turisticos',
            'realizar_reservas',
            'ver_mis_datos'
        ]);

        // Asignar permisos a Invitado
        $invitado->givePermissionTo([
            'ver_paquetes_turisticos',
            'ver_productos'
        ]);
    }
}

