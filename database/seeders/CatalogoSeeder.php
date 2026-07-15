<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Catalogo;

class CatalogoSeeder extends Seeder
{
    public function run()
    {
        // Listas base
        $listas = [
            'Zona de Sucursal' => [
                'Chiloe Norte',
                'Chiloe Centro',
                'Chiloe Sur',
                'Chin Chin',
                'Tepual',
                'Puerto Montt',
                'Decima Segunda',
            ],
            'Tipo de Sucursal' => [
                'Planta',
            ],
            'Tipo Empresa' => [
                'Productor',
                'Transportista',
            ],
        ];

        foreach ($listas as $categoriaNombre => $valores) {
            // Crear categoría (nivel superior)
            $categoria = Catalogo::create([
                'nombre' => $categoriaNombre,
                'catalogo_id' => null,
                'orden' => 0,
                'activo' => true,
            ]);

            // Crear valores asociados (nivel inferior)
            foreach ($valores as $i => $valorNombre) {
                Catalogo::create([
                    'nombre' => $valorNombre,
                    'catalogo_id' => $categoria->id,
                    'orden' => $i + 1,
                    'activo' => true,
                ]);
            }
        }
    }
}
