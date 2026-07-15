<?php

namespace Tests\Unit;

use App\Models\Empresa;
use App\Services\EmpresaConfiguration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmpresaConfigurationTest extends TestCase
{
    #[Test]
    public function company_routes_keep_their_administrative_middleware(): void
    {
        foreach (['empresa.index', 'empresa.store', 'empresa.update', 'empresa.destroy'] as $routeName) {
            $middleware = Route::getRoutes()->getByName($routeName)->gatherMiddleware();

            $this->assertContains('auth', $middleware);
            $this->assertContains(
                'check.role:' . config('constantes.ROL_COORDINADOR') . ',' . config('constantes.ROL_ADMINISTRADOR_IT'),
                $middleware
            );
        }
    }

    #[Test]
    public function only_catalogs_from_the_company_type_family_are_valid(): void
    {
        $this->prepareValidationTables();
        $rules = (new EmpresaConfiguration())->rules();
        $base = [
            'rut_empresa' => '11.111.111-1',
            'razon_social' => 'Empresa de prueba',
            'direccion' => 'Dirección de prueba',
            'comuna_id' => 0,
        ];

        $valid = Validator::make($base + ['tipo_empresa_id' => config('constantes.TIPO_EMPRESA_PRODUCTORA')], $rules);
        $foreign = Validator::make($base + ['tipo_empresa_id' => config('constantes.ROL_ADMINISTRADOR_IT')], $rules);

        $this->assertFalse($valid->fails());
        $this->assertTrue($foreign->fails());
        $this->assertArrayHasKey('tipo_empresa_id', $foreign->errors()->toArray());
    }

    #[Test]
    public function validated_company_data_excludes_unexpected_attributes(): void
    {
        $this->prepareValidationTables();
        $validator = Validator::make([
            'tipo_empresa_id' => config('constantes.TIPO_EMPRESA_PRODUCTORA'),
            'rut_empresa' => '11.111.111-1',
            'razon_social' => 'Empresa de prueba',
            'direccion' => 'Dirección de prueba',
            'comuna_id' => 0,
            'activo' => false,
            'observacion_inactividad' => 'Manipulado',
        ], (new EmpresaConfiguration())->rules());

        $this->assertArrayNotHasKey('activo', $validator->validated());
        $this->assertArrayNotHasKey('observacion_inactividad', $validator->validated());
    }

    #[Test]
    public function company_status_is_normalized_as_a_boolean(): void
    {
        $empresa = new Empresa(['activo' => 1]);

        $this->assertTrue($empresa->activo);
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
        Schema::create('comunas', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre');
        });

        DB::table('catalogos')->insert([
            [
                'id' => config('constantes.TIPO_EMPRESA_PRODUCTORA'),
                'catalogo_id' => config('constantes.CATEGORIA_TIPO_EMPRESA'),
                'nombre' => 'Productor',
                'activo' => true,
            ],
            [
                'id' => config('constantes.ROL_ADMINISTRADOR_IT'),
                'catalogo_id' => config('constantes.CATEGORIA_ROL_USUARIO'),
                'nombre' => 'Administrador IT',
                'activo' => true,
            ],
        ]);
        DB::table('comunas')->insert(['id' => 0, 'nombre' => 'No especificado']);
    }
}
