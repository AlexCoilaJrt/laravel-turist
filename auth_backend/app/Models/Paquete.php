<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    use HasFactory;

    // Si tienes algunos campos que no se deben asignar masivamente
    protected $fillable = ['nombre', 'descripcion', 'precio', 'fecha_inicio', 'fecha_fin'];

    // Si necesitas ocultar algunos campos al devolver el modelo en las respuestas JSON
    protected $hidden = ['created_at', 'updated_at'];

    // Si quieres mutadores para cambiar cómo se almacenan o devuelven ciertos valores
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = ucwords($value); // Capitaliza el nombre al guardarlo
    }
}
