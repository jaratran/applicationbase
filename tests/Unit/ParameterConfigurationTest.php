<?php

namespace Tests\Unit;

use App\Http\Controllers\Parametros\ParameterController;
use App\Models\DesignParameter;
use App\Models\OperationalParameter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class ParameterConfigurationTest extends TestCase
{
    #[Test]
    public function parameter_routes_keep_their_administrator_middleware(): void
    {
        $middleware = Route::getRoutes()->getByName('parameters.index')->gatherMiddleware();

        $this->assertContains('auth', $middleware);
        $this->assertContains('check.role:' . config('constantes.ROL_ADMINISTRADOR_IT'), $middleware);
    }

    #[Test]
    public function operational_booleans_are_normalized_by_the_model(): void
    {
        $parameters = new OperationalParameter();
        $parameters->audit_email_enabled = 1;
        $parameters->allow_profile_editing = 0;

        $this->assertTrue($parameters->audit_email_enabled);
        $this->assertFalse($parameters->allow_profile_editing);
    }

    #[Test]
    public function unexpected_attributes_are_not_mass_assignable(): void
    {
        $design = new DesignParameter();
        $operational = new OperationalParameter();

        $design->fill(['unexpected_setting' => 'value']);
        $operational->fill(['unexpected_setting' => 'value']);

        $this->assertNull($design->getAttribute('unexpected_setting'));
        $this->assertNull($operational->getAttribute('unexpected_setting'));
    }

    #[Test]
    public function missing_or_unsafe_image_names_fall_back_to_defaults(): void
    {
        $design = new DesignParameter();
        $design->setRawAttributes([
            'logo_design' => 'missing.png',
            'emblema_design' => '../outside.png',
        ]);

        $this->assertSame('default_logo.png', $design->logo_design);
        $this->assertSame('default_emblema.png', $design->emblema_design);
    }

    #[Test]
    public function replacing_an_image_never_deletes_a_default_asset(): void
    {
        $defaultPath = public_path('config/default_logo.png');
        $defaultHash = hash_file('sha256', $defaultPath);
        $storeMethod = new ReflectionMethod(ParameterController::class, 'storeDesignImage');
        $deleteMethod = new ReflectionMethod(ParameterController::class, 'deletePreviousDesignImage');
        $storedFile = null;

        try {
            $storedFile = $storeMethod->invoke(
                new ParameterController(),
                UploadedFile::fake()->create('replacement.png', 1, 'image/png'),
                'logo'
            );
            $deleteMethod->invoke(new ParameterController(), 'default_logo.png');

            $this->assertFileExists(public_path('config/' . $storedFile));
            $this->assertSame($defaultHash, hash_file('sha256', $defaultPath));
        } finally {
            if ($storedFile && is_file(public_path('config/' . $storedFile))) {
                unlink(public_path('config/' . $storedFile));
            }
        }
    }
}
