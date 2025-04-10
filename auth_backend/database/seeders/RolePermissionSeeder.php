<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Encuentra los roles
        $superAdmin = Role::findByName('SuperAdmin');
        $admin = Role::findByName('Admin');
        $emprendedor = Role::findByName('Emprendedor');
        $usuario = Role::findByName('Usuario');
        $invitado = Role::findByName('Invitado');

        // Asignar permisos a roles
        $superAdmin->givePermissionTo([
            'ver_paquetes', 'crear_editar_paquetes', 'aprobar_paquetes', 'reservar_paquete', 
            'ver_catalogo', 'crear_editar_productos', 'comprar_productos', 
            'ver_usuarios', 'crear_editar_usuarios', 'eliminar_usuarios', 'gestionar_roles_permisos', 
            'ver_empresas', 'crear_editar_empresas'
        ]);

        $admin->givePermissionTo([
            'ver_paquetes', 'crear_editar_paquetes', 'aprobar_paquetes', 'ver_catalogo', 
            'crear_editar_productos', 'comprar_productos', 'ver_usuarios', 
            'crear_editar_usuarios', 'eliminar_usuarios', 'ver_empresas', 'crear_editar_empresas'
        ]);

        $emprendedor->givePermissionTo([
            'ver_paquetes', 'crear_editar_paquetes', 'ver_catalogo', 
            'crear_editar_productos', 'comprar_productos', 'ver_empresas', 'crear_editar_empresas'
        ]);

        $usuario->givePermissionTo([
            'ver_paquetes', 'reservar_paquete', 'ver_catalogo', 'comprar_productos'
        ]);

        $invitado->givePermissionTo([
            'ver_paquetes', 'ver_catalogo'
        ]);
    }
}
