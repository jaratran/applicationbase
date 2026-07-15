<?php

namespace Tests\Feature;

use App\Http\Controllers\Actores\UsuarioController;
use App\Mail\ResetPasswordMailable;
use App\Mail\VerifyEmailMailable;
use App\Models\User;
use App\Notifications\CustomResetPassword;
use App\Notifications\CustomVerifyWelcomeEmail;
use App\Services\UserRoleAssignment;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MailFlowTest extends TestCase
{
    private string $compiledViews;

    protected function setUp(): void
    {
        parent::setUp();

        $this->compiledViews = storage_path('app/mail-test-views');
        File::ensureDirectoryExists($this->compiledViews);
        config(['view.compiled' => $this->compiledViews]);
        $this->app->forgetInstance('blade.compiler');

        $this->prepareTables();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->compiledViews);

        parent::tearDown();
    }

    #[Test]
    public function welcome_and_reset_mail_use_support_data_and_enabled_audit_bcc(): void
    {
        $user = $this->user();
        $welcome = new VerifyEmailMailable($user, 'https://applicationbase.test/verify', 'emails.welcome-generico');
        $reset = new ResetPasswordMailable($user, 'https://applicationbase.test/reset', 'emails.reset-password');

        $welcomeHtml = $welcome->render();
        $resetHtml = $reset->render();

        $this->assertTrue($welcome->hasBcc('audit@example.test'));
        $this->assertTrue($reset->hasBcc('audit@example.test'));
        $this->assertStringContainsString('support@example.test', $welcomeHtml);
        $this->assertStringContainsString('+56 9 1111 2222', $welcomeHtml);
        $this->assertStringContainsString('support@example.test', $resetHtml);
        $this->assertSame('Bienvenido a ApplicationBase', $welcome->subject);
        $this->assertSame('Restablecer contraseña en ApplicationBase', $reset->subject);
    }

    #[Test]
    public function mail_omits_audit_bcc_when_the_option_is_disabled(): void
    {
        DB::table('operational_parameters')->where('id', 1)->update(['audit_email_enabled' => false]);
        $user = $this->user();
        $welcome = new VerifyEmailMailable($user, 'https://applicationbase.test/verify', 'emails.welcome-generico');
        $reset = new ResetPasswordMailable($user, 'https://applicationbase.test/reset', 'emails.reset-password');

        $welcome->render();
        $reset->render();

        $this->assertFalse($welcome->hasBcc('audit@example.test'));
        $this->assertFalse($reset->hasBcc('audit@example.test'));
    }

    #[Test]
    public function administrator_can_resend_welcome_only_to_an_active_unverified_user(): void
    {
        Notification::fake();
        Auth::setUser($this->administrator());
        $target = $this->persistedUser();
        $controller = new UsuarioController();

        $response = $controller->resendWelcomeEmail($target->id, new UserRoleAssignment());

        $this->assertSame(200, $response->getStatusCode());
        Notification::assertSentTo($target, CustomVerifyWelcomeEmail::class);

        Notification::fake();
        $target->forceFill(['email_verified_at' => now()])->save();
        $verifiedResponse = $controller->resendWelcomeEmail($target->id, new UserRoleAssignment());

        $this->assertSame(422, $verifiedResponse->getStatusCode());
        Notification::assertNothingSent();

        Notification::fake();
        $target->forceFill(['email_verified_at' => null, 'activo' => false])->save();
        $inactiveResponse = $controller->resendWelcomeEmail($target->id, new UserRoleAssignment());

        $this->assertSame(422, $inactiveResponse->getStatusCode());
        Notification::assertNothingSent();
    }

    #[Test]
    public function authenticated_user_can_resend_own_verification_with_the_custom_notification(): void
    {
        Notification::fake();
        $user = $this->persistedUser();

        $response = $this->actingAs($user)
            ->from(route('panel.index'))
            ->post(route('verification.resend'));

        $response->assertRedirect(route('panel.index'));
        $response->assertSessionHas('resent', true);
        Notification::assertSentTo($user, CustomVerifyWelcomeEmail::class);
    }

    #[Test]
    public function password_recovery_route_dispatches_the_custom_notification(): void
    {
        Notification::fake();
        $user = $this->persistedUser(verified: true);
        $route = Route::getRoutes()->getByName('password.email');

        $result = Password::broker()->sendResetLink(['email' => $user->email]);

        $this->assertSame('forget-password', $route->uri());
        $this->assertSame(Password::RESET_LINK_SENT, $result);
        Notification::assertSentTo($user, CustomResetPassword::class);
    }

    private function administrator(): User
    {
        $user = $this->user();
        $user->id = 999;
        $user->rol_id = config('constantes.ROL_ADMINISTRADOR_IT');

        return $user;
    }

    private function persistedUser(bool $verified = false): User
    {
        $user = $this->user();
        $user->activo = true;
        $user->password = bcrypt('password');
        $user->email_verified_at = $verified ? now() : null;
        $user->save();

        return $user;
    }

    private function user(): User
    {
        return new User([
            'rut_usuario' => '00.000.010-8',
            'nombre_usuario' => 'Usuario',
            'apellidos_usuario' => 'Ejemplo',
            'rol_id' => config('constantes.ROL_PERSONAL_GERENCIA'),
            'email' => 'user@example.test',
            'comuna_id' => 0,
            'activo' => true,
        ]);
    }

    private function prepareTables(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('rut_usuario');
            $table->string('nombre_usuario');
            $table->string('apellidos_usuario');
            $table->unsignedBigInteger('rol_id');
            $table->string('email')->unique();
            $table->unsignedBigInteger('comuna_id');
            $table->boolean('activo')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('password_resets', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
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
            $table->string('support_email')->nullable();
            $table->string('support_telefono')->nullable();
            $table->string('audit_email')->nullable();
            $table->boolean('audit_email_enabled')->default(false);
            $table->unsignedInteger('verification_expiration_time')->nullable();
            $table->boolean('allow_profile_editing')->default(true);
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
        DB::table('operational_parameters')->insert([
            'id' => 1,
            'support_email' => 'support@example.test',
            'support_telefono' => '+56 9 1111 2222',
            'audit_email' => 'audit@example.test',
            'audit_email_enabled' => true,
            'verification_expiration_time' => 60,
            'allow_profile_editing' => true,
        ]);
    }
}
