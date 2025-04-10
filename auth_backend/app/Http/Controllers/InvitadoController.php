<?php

namespace App\Http\Controllers;

use App\Models\Paquete; // Asumiendo que tienes un modelo Paquete
use App\Models\Producto; // Asumiendo que tienes un modelo Producto
use Illuminate\Http\Request;

class InvitadoController extends Controller
{
    // Método para ver los paquetes turísticos
    public function verPaquetes()
    {
        // Obtenemos todos los paquetes turísticos disponibles
        $paquetes = Paquete::all(); // O cualquier lógica de filtrado que desees aplicar

        return response()->json($paquetes);
    }

    // Método para ver el catálogo de productos (artesanías y comidas)
    public function verCatalogo()
    {
        // Obtenemos todos los productos disponibles
        $productos = Producto::all(); // O cualquier lógica de filtrado que desees aplicar

        return response()->json($productos);
    }
}
