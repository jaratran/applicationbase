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

    private function user(int $roleId): User
    {
        $user = new User();
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
