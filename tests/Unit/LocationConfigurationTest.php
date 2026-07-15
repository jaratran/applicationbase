<?php

namespace Tests\Unit;

use App\Http\Controllers\Parametros\LocationController;
use App\Models\Comuna;
use App\Models\Region;
use App\Services\LocationConfiguration;
use Database\Seeders\ComunaSeeder;
use Database\Seeders\RegionSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LocationConfigurationTest extends TestCase
{
    #[Test]
    public function territorial_endpoints_require_authentication(): void
    {
        foreach (['/parametros/region', '/parametros/region-operativa', '/parametros/comuna?idRegion=1'] as $uri) {
            $route = Route::getRoutes()->match(Request::create($uri, 'GET'));

            $this->assertContains('auth', $route->gatherMiddleware());
        }
    }

    #[Test]
    public function commune_must_belong_to_the_selected_region(): void
    {
        $this->prepareTables();
        $configuration = new LocationConfiguration();

        $valid = Validator::make(['comuna_id' => 10], [
            'comuna_id' => $configuration->communeRule(1),
        ]);
        $foreign = Validator::make(['comuna_id' => 11], [
            'comuna_id' => $configuration->communeRule(1),
        ]);

        $this->assertFalse($valid->fails());
        $this->assertTrue($foreign->fails());
    }

    #[Test]
    public function nonexistent_or_neutral_regions_are_not_selectable(): void
    {
        $this->prepareTables();
        $rule = (new LocationConfiguration())->regionRule();

        $this->assertFalse(Validator::make(['region_id' => 1], ['region_id' => $rule])->fails());
        $this->assertTrue(Validator::make(['region_id' => 0], ['region_id' => $rule])->fails());
        $this->assertTrue(Validator::make(['region_id' => 999], ['region_id' => $rule])->fails());
    }

    #[Test]
    public function commune_endpoint_filters_orders_and_limits_its_response(): void
    {
        $this->prepareTables();
        $result = (new LocationController())->obtenerComuna(
            Request::create('/parametros/comuna', 'GET', ['idRegion' => 1]),
            new LocationConfiguration()
        );

        $this->assertSame([
            ['id' => 10, 'nombre' => 'Comuna A'],
            ['id' => 12, 'nombre' => 'Comuna C'],
        ], $result->toArray());
    }

    #[Test]
    public function commune_endpoint_rejects_an_unknown_region(): void
    {
        $this->prepareTables();
        $this->expectException(ValidationException::class);

        (new LocationController())->obtenerComuna(
            Request::create('/parametros/comuna', 'GET', ['idRegion' => 999]),
            new LocationConfiguration()
        );
    }

    #[Test]
    public function operational_region_endpoint_excludes_neutral_and_non_operational_records(): void
    {
        $this->prepareTables();
        $controller = new LocationController();

        $this->assertSame([
            ['id' => 1, 'nombre' => 'Región uno'],
            ['id' => 2, 'nombre' => 'Región dos'],
        ], $controller->obtenerRegion()->toArray());
        $this->assertSame([
            ['id' => 1, 'nombre' => 'Región uno'],
        ], $controller->obtenerRegionOperativa()->toArray());
    }

    #[Test]
    public function models_expose_current_relations_casts_and_safe_fillable_attributes(): void
    {
        $this->prepareTables();
        $region = Region::with('comunas')->findOrFail(1);
        $commune = Comuna::with('region')->findOrFail(10);
        $unexpectedRegion = new Region(['id' => 99, 'nombre' => 'Prueba', 'orden' => '3', 'operativa' => 1]);
        $unexpectedCommune = new Comuna(['id' => 99, 'nombre' => 'Prueba', 'region_id' => '1', 'created_at' => now()]);

        $this->assertCount(2, $region->comunas);
        $this->assertSame(1, $commune->region->id);
        $this->assertSame(3, $unexpectedRegion->orden);
        $this->assertTrue($unexpectedRegion->operativa);
        $this->assertSame(1, $unexpectedCommune->region_id);
        $this->assertNull($unexpectedRegion->id);
        $this->assertNull($unexpectedCommune->id);
        $this->assertNull($unexpectedCommune->created_at);
    }

    #[Test]
    public function minimal_territorial_seeders_are_ordered_and_repeatable(): void
    {
        $this->prepareTables(withoutData: true);
        $regionSeeder = new RegionSeeder();
        $communeSeeder = new ComunaSeeder();

        $regionSeeder->run();
        $communeSeeder->run();
        $regionSeeder->run();
        $communeSeeder->run();

        $this->assertSame(1, DB::table('regiones')->count());
        $this->assertSame(1, DB::table('comunas')->count());
        $this->assertSame(0, DB::table('comunas')->value('region_id'));
        $this->assertSame('No especificado', DB::table('regiones')->value('nombre'));
        $this->assertSame('No especificado', DB::table('comunas')->value('nombre'));
    }

    private function prepareTables(bool $withoutData = false): void
    {
        Schema::create('regiones', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre');
            $table->integer('orden');
            $table->boolean('operativa')->default(false);
            $table->timestamps();
        });
        Schema::create('comunas', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre');
            $table->unsignedBigInteger('region_id');
            $table->timestamps();
        });

        if ($withoutData) {
            return;
        }

        DB::table('regiones')->insert([
            ['id' => 0, 'nombre' => 'No especificado', 'orden' => 0, 'operativa' => false],
            ['id' => 1, 'nombre' => 'Región uno', 'orden' => 1, 'operativa' => true],
            ['id' => 2, 'nombre' => 'Región dos', 'orden' => 2, 'operativa' => false],
        ]);
        DB::table('comunas')->insert([
            ['id' => 10, 'nombre' => 'Comuna A', 'region_id' => 1],
            ['id' => 11, 'nombre' => 'Comuna B', 'region_id' => 2],
            ['id' => 12, 'nombre' => 'Comuna C', 'region_id' => 1],
        ]);
    }
}
