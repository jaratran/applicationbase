<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $sqlModes = array_filter(explode(',', (string) DB::scalar('SELECT @@SESSION.sql_mode')));
        if (!in_array('NO_AUTO_VALUE_ON_ZERO', $sqlModes, true)) {
            $sqlModes[] = 'NO_AUTO_VALUE_ON_ZERO';
            DB::statement('SET SESSION sql_mode = ?', [implode(',', $sqlModes)]);
        }

        $this->call(CatalogoSeeder::class);

        DB::table('regiones')->updateOrInsert(
            ['id' => 0],
            ['nombre' => 'No especificado', 'orden' => 0, 'operativa' => false, 'updated_at' => now()]
        );
        DB::table('comunas')->updateOrInsert(
            ['id' => 0],
            ['nombre' => 'No especificado', 'region_id' => 0, 'updated_at' => now()]
        );

        DB::table('design_parameters')->updateOrInsert(
            ['id' => 1],
            [
                'titulo_design' => 'ApplicationBase',
                'logo_design' => 'default_logo.png',
                'emblema_design' => 'default_emblema.png',
                'favicon_design' => 'default_favicon.ico',
                'fondo_pantalla_design' => 'default_fondo.png',
                'custom_primary' => '#0d6efd',
                'custom_secondary' => '#6c757d',
                'custom_success' => '#198754',
                'custom_warning' => '#ffc107',
                'custom_danger' => '#dc3545',
                'custom_info' => '#0dcaf0',
                'updated_at' => now(),
            ]
        );
        DB::table('operational_parameters')->updateOrInsert(
            ['id' => 1],
            [
                'audit_email_enabled' => false,
                'allow_profile_editing' => true,
                'verification_expiration_time' => 60,
                'updated_at' => now(),
            ]
        );

        $this->call([
            SucursalSeeder::class,
            EmpresaSeeder::class,
            MaquilaSeeder::class,
        ]);

        if (!User::where('email', 'admin@applicationbase.local')->exists()) {
            $rolId = DB::table('catalogos')->where('nombre', 'Administrador IT')->value('id');
            $comunaId = DB::table('comunas')->where('nombre', 'No especificado')->value('id');

            if ($rolId === null || $comunaId === null) {
                throw new RuntimeException('Faltan el rol o la comuna requeridos para crear el administrador inicial.');
            }

            $password = Str::password(20);
            $user = User::create([
                'rut_usuario' => '00.000.001-9',
                'nombre_usuario' => 'Administrador',
                'apellidos_usuario' => 'ApplicationBase',
                'rol_id' => $rolId,
                'email' => 'admin@applicationbase.local',
                'comuna_id' => $comunaId,
                'activo' => true,
                'password' => Hash::make($password),
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();

            $this->command->warn("Administrador inicial: admin@applicationbase.local / {$password}");
        }
    }
}
