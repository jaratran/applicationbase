<?php

namespace Tests\Unit;

use App\Models\Empresa;
use App\Models\Maquila;
use App\Models\Sucursal;
use App\Services\MaquilaConfiguration;
use Database\Seeders\MaquilaSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MaquilaConfigurationTest extends TestCase
{
    #[Test]
    public function association_routes_keep_their_administrative_middleware(): void
    {
        foreach (['empresa.plantas', 'empresa.plantas.guardar', 'sucursal.productoras', 'sucursal.productoras.guardar'] as $routeName) {
            $middleware = Route::getRoutes()->getByName($routeName)->gatherMiddleware();

            $this->assertContains('auth', $middleware);
            $this->assertContains(
                'check.role:' . config('constantes.ROL_COORDINADOR') . ',' . config('constantes.ROL_ADMINISTRADOR_IT'),
                $middleware
            );
        }
    }

    #[Test]
    public function valid_active_producer_and_plant_can_be_associated(): void
    {
        $this->prepareTables();
        $configuration = new MaquilaConfiguration();

        $companies = Validator::make(['empresas' => [100]], $configuration->companyRules());
        $branches = Validator::make(['sucursales' => [200]], $configuration->branchRules());

        $this->assertFalse($companies->fails());
        $this->assertFalse($branches->fails());
    }

    #[Test]
    public function duplicate_association_ids_are_rejected(): void
    {
        $this->prepareTables();
        $configuration = new MaquilaConfiguration();

        $companies = Validator::make(['empresas' => [100, 100]], $configuration->companyRules());
        $branches = Validator::make(['sucursales' => [200, 200]], $configuration->branchRules());

        $this->assertTrue($companies->fails());
        $this->assertTrue($branches->fails());
    }

    #[Test]
    public function invalid_inactive_or_wrongly_classified_entities_are_rejected(): void
    {
        $this->prepareTables();
        $configuration = new MaquilaConfiguration();

        foreach ([101, 102, 999] as $companyId) {
            $this->assertTrue(Validator::make(['empresas' => [$companyId]], $configuration->companyRules())->fails());
        }
        foreach ([201, 202, 999] as $branchId) {
            $this->assertTrue(Validator::make(['sucursales' => [$branchId]], $configuration->branchRules())->fails());
        }
    }

    #[Test]
    public function unexpected_attributes_are_not_validated_for_persistence(): void
    {
        $this->prepareTables();
        $validator = Validator::make([
            'empresas' => [100],
            'activo' => false,
            'observaciones' => 'Manipulada',
        ], (new MaquilaConfiguration())->companyRules());

        $this->assertArrayNotHasKey('activo', $validator->validated());
        $this->assertArrayNotHasKey('observaciones', $validator->validated());
    }

    #[Test]
    public function only_producer_companies_and_plant_branches_expose_association_actions(): void
    {
        $configuration = new MaquilaConfiguration();

        $this->assertTrue($configuration->canAssociateBranches(new Empresa(['tipo_empresa_id' => config('constantes.TIPO_EMPRESA_PRODUCTORA')])));
        $this->assertFalse($configuration->canAssociateBranches(new Empresa(['tipo_empresa_id' => config('constantes.TIPO_EMPRESA_TRANSPORTISTA')])));
        $this->assertTrue($configuration->canAssociateCompanies(new Sucursal(['tipo_sucursal_id' => config('constantes.TIPO_SUCURSAL_PLANTA')])));
        $this->assertFalse($configuration->canAssociateCompanies(new Sucursal(['tipo_sucursal_id' => 999])));
    }

    #[Test]
    public function inactive_associations_are_preserved_without_duplicates(): void
    {
        $ids = (new MaquilaConfiguration())->preserveInactiveAssociations([2, 3], [1, 2]);

        $this->assertSame([2, 3, 1], $ids);
    }

    #[Test]
    public function model_normalizes_relation_attributes(): void
    {
        $association = new Maquila([
            'empresa_id' => '100',
            'sucursal_id' => '200',
            'fecha_inicio' => '2026-07-15',
            'activo' => 1,
        ]);

        $this->assertSame(100, $association->empresa_id);
        $this->assertSame(200, $association->sucursal_id);
        $this->assertSame('2026-07-15', $association->fecha_inicio->toDateString());
        $this->assertTrue($association->activo);
    }

    #[Test]
    public function seeder_creates_one_repeatable_semantic_association(): void
    {
        $this->prepareTables(true);
        $seeder = new MaquilaSeeder();

        $seeder->run();
        $seeder->run();

        $this->assertSame(1, DB::table('maquilas')->count());
        $this->assertSame(100, DB::table('maquilas')->value('empresa_id'));
        $this->assertSame(200, DB::table('maquilas')->value('sucursal_id'));
    }

    private function prepareTables(bool $withPivot = false): void
    {
        Schema::create('empresas', function (Blueprint $table): void {
            $table->id();
            $table->string('razon_social');
            $table->unsignedBigInteger('tipo_empresa_id');
            $table->boolean('activo')->default(true);
        });
        Schema::create('sucursales', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre_sucursal');
            $table->unsignedBigInteger('tipo_sucursal_id');
            $table->boolean('activo')->default(true);
        });

        DB::table('empresas')->insert([
            ['id' => 100, 'razon_social' => 'Empresa Productora Base', 'tipo_empresa_id' => config('constantes.TIPO_EMPRESA_PRODUCTORA'), 'activo' => true],
            ['id' => 101, 'razon_social' => 'Empresa Transportista', 'tipo_empresa_id' => config('constantes.TIPO_EMPRESA_TRANSPORTISTA'), 'activo' => true],
            ['id' => 102, 'razon_social' => 'Empresa Inactiva', 'tipo_empresa_id' => config('constantes.TIPO_EMPRESA_PRODUCTORA'), 'activo' => false],
        ]);
        DB::table('sucursales')->insert([
            ['id' => 200, 'nombre_sucursal' => 'Sucursal Principal', 'tipo_sucursal_id' => config('constantes.TIPO_SUCURSAL_PLANTA'), 'activo' => true],
            ['id' => 201, 'nombre_sucursal' => 'Sucursal Inactiva', 'tipo_sucursal_id' => config('constantes.TIPO_SUCURSAL_PLANTA'), 'activo' => false],
            ['id' => 202, 'nombre_sucursal' => 'Sucursal de otro tipo', 'tipo_sucursal_id' => 999, 'activo' => true],
        ]);

        if ($withPivot) {
            Schema::create('maquilas', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('empresa_id');
                $table->unsignedBigInteger('sucursal_id');
                $table->timestamps();
                $table->unique(['empresa_id', 'sucursal_id']);
            });
        }
    }
}
