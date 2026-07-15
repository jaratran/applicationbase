<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        // Estos IDs son parte del contrato público definido en config/constantes.php.
        $catalogos = [
            [0, null, 'No especificado', 0],
            [1, null, 'Rol de Usuario', 1],
            [20, null, 'Tipo de Relación', 2],
            [21, null, 'Zona de Sucursal', 3],
            [29, null, 'Tipo de Sucursal', 4],
            [31, null, 'Tipo Empresa', 5],
            [130, null, 'Región Operativa', 13],
            [180, null, 'No especificado', 0],

            [22, 21, 'Chiloe Norte', 1],
            [23, 21, 'Chiloe Centro', 2],
            [24, 21, 'Chiloe Sur', 3],
            [25, 21, 'Chin Chin', 4],
            [26, 21, 'Tepual', 5],
            [27, 21, 'Puerto Montt', 6],
            [28, 21, 'Decima Segunda', 7],
            [30, 29, 'Planta', 1],
            [32, 31, 'Productor', 1],
            [33, 31, 'Transportista', 2],
            [57, 20, 'Agrupa', 1],

            [61, 1, 'Solicitante Planta', 1],
            [71, 1, 'Solicitante Planta XII', 2],
            [62, 1, 'Solicitante Productor', 3],
            [63, 1, 'Personal Gerencia', 4],
            [64, 1, 'Personal Producción', 5],
            [65, 1, 'Personal Calidad', 6],
            [66, 1, 'Coordinador', 7],
            [70, 1, 'Coordinador XII', 8],
            [67, 1, 'Personal Mantención', 9],
            [68, 1, 'Personal Romana', 10],
            [69, 1, 'Administrador IT', 11],

            [191, 130, 'X Región', 1],
            [192, 130, 'XII Región', 2],
        ];

        foreach ($catalogos as [$id, $catalogoId, $nombre, $orden]) {
            DB::table('catalogos')->updateOrInsert(
                ['id' => $id],
                [
                    'catalogo_id' => $catalogoId,
                    'nombre' => $nombre,
                    'orden' => $orden,
                    'activo' => true,
                    'updated_at' => now(),
                ]
            );
        }
    }
}
