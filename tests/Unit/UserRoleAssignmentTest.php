<?php

namespace Tests\Unit;

use App\Models\Catalogo;
use App\Models\User;
use App\Services\UserRoleAssignment;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserRoleAssignmentTest extends TestCase
{
    private UserRoleAssignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->assignment = new UserRoleAssignment();
    }

    #[Test]
    public function administrator_can_assign_any_active_user_role(): void
    {
        $this->assertTrue($this->assignment->canAssignRole(
            $this->user(config('constantes.ROL_ADMINISTRADOR_IT')),
            $this->role(config('constantes.ROL_ADMINISTRADOR_IT')),
        ));
    }

    #[Test]
    public function coordinator_can_assign_a_lower_authority_role(): void
    {
        $this->assertTrue($this->assignment->canAssignRole(
            $this->user(config('constantes.ROL_COORDINADOR')),
            $this->role(config('constantes.ROL_PERSONAL_PRODUCCION')),
        ));
    }

    #[Test]
    public function coordinator_cannot_assign_administrator_or_coordinator(): void
    {
        $coordinator = $this->user(config('constantes.ROL_COORDINADOR'));

        $this->assertFalse($this->assignment->canAssignRole($coordinator, $this->role(config('constantes.ROL_ADMINISTRADOR_IT'))));
        $this->assertFalse($this->assignment->canAssignRole($coordinator, $this->role(config('constantes.ROL_COORDINADOR'))));
    }

    #[Test]
    public function coordinator_cannot_manage_an_administrator(): void
    {
        $this->assertFalse($this->assignment->canManageUser(
            $this->user(config('constantes.ROL_COORDINADOR')),
            $this->user(config('constantes.ROL_ADMINISTRADOR_IT')),
        ));
    }

    #[Test]
    public function a_catalog_outside_the_role_family_is_rejected(): void
    {
        $role = $this->role(config('constantes.ROL_PERSONAL_PRODUCCION'));
        $role->catalogo_id = config('constantes.CATEGORIA_TIPO_EMPRESA');

        $this->assertFalse($this->assignment->canAssignRole(
            $this->user(config('constantes.ROL_ADMINISTRADOR_IT')),
            $role,
        ));
    }

    #[Test]
    public function coordinator_can_update_a_peer_without_changing_the_peer_role(): void
    {
        $coordinator = $this->user(config('constantes.ROL_COORDINADOR'));
        $target = $this->user(config('constantes.ROL_COORDINADOR'));

        $this->assertTrue($this->assignment->canAssignRole(
            $coordinator,
            $this->role(config('constantes.ROL_COORDINADOR')),
            $target,
        ));
        $this->assertFalse($this->assignment->canAssignRole(
            $coordinator,
            $this->role(config('constantes.ROL_PERSONAL_PRODUCCION')),
            $target,
        ));
    }

    #[Test]
    public function administrator_can_change_another_users_status(): void
    {
        $administrator = $this->user(config('constantes.ROL_ADMINISTRADOR_IT'), 1);
        $target = $this->user(config('constantes.ROL_PERSONAL_PRODUCCION'), 2);

        $this->assertTrue($this->assignment->canChangeStatus($administrator, $target));
    }

    #[Test]
    public function coordinator_can_administer_a_lower_authority_user_but_not_an_administrator(): void
    {
        $coordinator = $this->user(config('constantes.ROL_COORDINADOR'), 1);

        $this->assertTrue($this->assignment->canChangeStatus(
            $coordinator,
            $this->user(config('constantes.ROL_PERSONAL_PRODUCCION'), 2),
        ));
        $this->assertFalse($this->assignment->canChangeStatus(
            $coordinator,
            $this->user(config('constantes.ROL_ADMINISTRADOR_IT'), 3),
        ));
    }

    #[Test]
    public function coordinator_can_change_another_coordinators_status(): void
    {
        $this->assertTrue($this->assignment->canChangeStatus(
            $this->user(config('constantes.ROL_COORDINADOR'), 1),
            $this->user(config('constantes.ROL_COORDINADOR'), 2),
        ));
    }

    #[Test]
    public function an_administrator_cannot_deactivate_itself(): void
    {
        $administrator = $this->user(config('constantes.ROL_ADMINISTRADOR_IT'), 1);

        $this->assertFalse($this->assignment->canChangeStatus($administrator, $administrator));
    }

    #[Test]
    public function welcome_can_only_be_resent_to_an_active_unverified_manageable_user(): void
    {
        $administrator = $this->user(config('constantes.ROL_ADMINISTRADOR_IT'), 1);
        $target = $this->user(config('constantes.ROL_PERSONAL_PRODUCCION'), 2);
        $target->activo = true;

        $this->assertTrue($this->assignment->canResendWelcome($administrator, $target));

        $target->email_verified_at = now();
        $this->assertFalse($this->assignment->canResendWelcome($administrator, $target));

        $target->email_verified_at = null;
        $target->activo = false;
        $this->assertFalse($this->assignment->canResendWelcome($administrator, $target));
    }

    #[Test]
    public function historical_admin_flag_does_not_grant_authority_to_an_ordinary_role(): void
    {
        $ordinaryUser = $this->user(config('constantes.ROL_PERSONAL_PRODUCCION'), 1);
        $ordinaryUser->setAttribute('es_admin', 1);
        $target = $this->user(config('constantes.ROL_PERSONAL_PRODUCCION'), 2);

        $this->assertFalse($this->assignment->canManageUser($ordinaryUser, $target));
        $this->assertFalse($this->assignment->canChangeStatus($ordinaryUser, $target));
    }

    #[Test]
    public function legacy_authority_attributes_cannot_be_mass_assigned(): void
    {
        $user = new User();
        $user->fill([
            'es_admin' => 1,
            'activated' => true,
        ]);

        $this->assertNull($user->getAttribute('es_admin'));
        $this->assertNull($user->getAttribute('activated'));
    }

    private function user(int $roleId, ?int $id = null): User
    {
        $user = new User();
        $user->id = $id;
        $user->rol_id = $roleId;

        return $user;
    }

    private function role(int $id): Catalogo
    {
        $role = new Catalogo();
        $role->id = $id;
        $role->catalogo_id = config('constantes.CATEGORIA_ROL_USUARIO');
        $role->activo = true;

        return $role;
    }
}
