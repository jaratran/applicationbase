<?php

namespace Tests\Feature;

use App\Http\Controllers\PanelController;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PanelTest extends TestCase
{
    #[Test]
    public function panel_route_is_available_to_every_authenticated_user(): void
    {
        $middleware = Route::getRoutes()->getByName('panel.index')->gatherMiddleware();

        $this->assertContains('auth', $middleware);
        $this->assertFalse(collect($middleware)->contains(fn (string $item) => str_starts_with($item, 'check.role:')));
    }

    #[Test]
    public function guest_is_redirected_from_panel_to_login(): void
    {
        $this->get(route('panel.index'))->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticated_ordinary_user_can_open_the_demonstration_panel(): void
    {
        $this->prepareViewParameters();
        $compiledViews = storage_path('app/panel-test-views');
        File::ensureDirectoryExists($compiledViews);
        config(['view.compiled' => $compiledViews]);
        $this->app->forgetInstance('blade.compiler');
        $user = new User([
            'nombre_usuario' => 'Usuario',
            'email' => 'usuario@example.com',
            'rol_id' => config('constantes.ROL_PERSONAL_GERENCIA'),
        ]);

        try {
            $response = $this->actingAs($user)->get(route('panel.index'));

            $response->assertOk();
            $response->assertSee('Bienvenido/a, Usuario');
            $response->assertSee('Ver mi perfil');
            $response->assertSee('Contenido demostrativo de los componentes disponibles');
            $response->assertSee('actividadMensualChart', false);
            $response->assertSee('distribucionEstadosChart', false);
            $response->assertSee('panelRegistrosTable', false);
            $this->assertSame(4, substr_count($response->getContent(), 'data-panel-kpi'));

            foreach (['Solicitudes de Retiro', 'Programa Diario', 'Planificaciones', 'Camiones', 'Conductores', 'Ramplas'] as $domainTerm) {
                $response->assertDontSee($domainTerm);
            }
        } finally {
            File::deleteDirectory($compiledViews);
        }
    }

    #[Test]
    public function controller_does_not_prepare_metrics_or_domain_data(): void
    {
        $view = (new PanelController())->index();

        $this->assertSame('panel', $view->name());
        $this->assertSame([], $view->getData());
    }

    #[Test]
    public function post_login_destination_remains_the_panel(): void
    {
        $this->assertSame('panel', Route::getRoutes()->getByName('panel.index')->uri());
    }

    private function prepareViewParameters(): void
    {
        Schema::create('design_parameters', function (Blueprint $table): void {
            $table->id();
            $table->string('titulo_design');
            $table->string('logo_design');
            $table->string('emblema_design');
            $table->string('favicon_design');
            $table->string('fondo_pantalla_design');
            $table->string('custom_primary');
            $table->string('custom_secondary');
            $table->string('custom_success');
            $table->string('custom_warning');
            $table->string('custom_danger');
            $table->string('custom_info');
            $table->timestamps();
        });
        Schema::create('operational_parameters', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
        });

        DB::table('design_parameters')->insert([
            'id' => 1,
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
        ]);
    }
}
