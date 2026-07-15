<?php

namespace Tests\Unit;

use App\Models\Sucursal;
use App\Services\SucursalConfiguration;
use App\Services\LocationConfiguration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SucursalConfigurationTest extends TestCase
{
    #[Test]
    public function branch_routes_keep_their_administrative_middleware(): void
    {
        foreach (['sucursal.index', 'sucursal.store', 'sucursal.update', 'sucursal.destroy'] as $routeName) {
            $middleware = Route::getRoutes()->getByName($routeName)->gatherMiddleware();

            $this->assertContains('auth', $middleware);
            $this->assertContains(
                'check.role:' . config('constantes.ROL_COORDINADOR') . ',' . config('constantes.ROL_ADMINISTRADOR_IT'),
                $middleware
            );
        }
    }

    #[Test]
    public function type_and_zone_must_belong_to_their_catalog_families(): void
    {
        $this->prepareValidationTables();
        $configuration = new SucursalConfiguration(new LocationConfiguration());
        $validData = $this->validData();
        $valid = Validator::make($validData, $configuration->rules(Request::create('/', 'POST', $validData)));
        $foreignData = $validData + [];
        $foreignData['tipo_sucursal_id'] = config('constantes.ROL_ADMINISTRADOR_IT');
        $foreignData['zona_id'] = config('constantes.TIPO_SUCURSAL_PLANTA');
        $foreign = Validator::make($foreignData, $configuration->rules(Request::create('/', 'POST', $foreignData)));

        $this->assertFalse($valid->fails());
        $this->assertTrue($foreign->fails());
        $this->assertArrayHasKey('tipo_sucursal_id', $foreign->errors()->toArray());
        $this->assertArrayHasKey('zona_id', $foreign->errors()->toArray());
    }

    #[Test]
    public function commune_must_belong_to_the_submitted_region(): void
    {
        $this->prepareValidationTables();
        $data = $this->validData();
        $data['region_id'] = 2;
        $configuration = new SucursalConfiguration(new LocationConfiguration());
        $validator = Validator::make($data, $configuration->rules(Request::create('/', 'POST', $data)));

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('comuna_id', $validator->errors()->toArray());
    }

    #[Test]
    public function only_expected_branch_attributes_are_persisted(): void
    {
        $configuration = new SucursalConfiguration(new LocationConfiguration());
        $data = $configuration->persistableData($this->validData() + [
            'activo' => false,
            'observacion_inactividad' => 'Manipulada',
        ]);

        $this->assertArrayNotHasKey('region_id', $data);
        $this->assertArrayNotHasKey('activo', $data);
        $this->assertArrayNotHasKey('observacion_inactividad', $data);
    }

    #[Test]
    public function branch_status_and_numeric_fields_are_normalized(): void
    {
        $sucursal = new Sucursal([
            'activo' => 1,
            'km' => '12',
            'tiempo_estimado_viaje' => '15.50',
        ]);

        $this->assertTrue($sucursal->activo);
        $this->assertSame(12, $sucursal->km);
        $this->assertSame('15.50', $sucursal->tiempo_estimado_viaje);
    }

    private function validData(): array
    {
        return [
            'zona_id' => 27,
            'nombre_sucursal' => 'Sucursal de prueba',
            'tipo_sucursal_id' => config('constantes.TIPO_SUCURSAL_PLANTA'),
            'region_id' => 1,
            'comuna_id' => 10,
        ];
    }

    private function prepareValidationTables(): void
    {
        Schema::create('catalogos', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('catalogo_id')->nullable();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->softDeletes();
        });
        Schema::create('regiones', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre');
            $table->boolean('operativa')->default(true);
        });
        Schema::create('comunas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('region_id');
            $table->string('nombre');
        });

        DB::table('catalogos')->insert([
            ['id' => 27, 'catalogo_id' => config('constantes.CATEGORIA_ZONA_SUCURSAL'), 'nombre' => 'Puerto Montt'],
            ['id' => config('constantes.TIPO_SUCURSAL_PLANTA'), 'catalogo_id' => config('constantes.CATEGORIA_TIPO_SUCURSAL'), 'nombre' => 'Planta'],
            ['id' => config('constantes.ROL_ADMINISTRADOR_IT'), 'catalogo_id' => config('constantes.CATEGORIA_ROL_USUARIO'), 'nombre' => 'Administrador IT'],
        ]);
        DB::table('regiones')->insert([
            ['id' => 1, 'nombre' => 'Región uno', 'operativa' => true],
            ['id' => 2, 'nombre' => 'Región dos', 'operativa' => true],
        ]);
        DB::table('comunas')->insert(['id' => 10, 'region_id' => 1, 'nombre' => 'Comuna uno']);
    }
}
