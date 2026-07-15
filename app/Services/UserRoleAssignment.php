<?php

namespace App\Services;

use App\Models\Catalogo;
use App\Models\User;
use Illuminate\Support\Collection;

class UserRoleAssignment
{
    public function assignableRoles(User $actor, ?User $target = null): Collection
    {
        return Catalogo::query()
            ->where('catalogo_id', config('constantes.CATEGORIA_ROL_USUARIO'))
            ->activos()
            ->get()
            ->filter(fn (Catalogo $role) => $this->canAssignRole($actor, $role, $target))
            ->values();
    }

    public function canManageUser(User $actor, User $target): bool
    {
        if ($this->isAdministrator($actor)) {
            return true;
        }

        return $this->isCoordinator($actor) && !$this->isAdministrator($target);
    }

    public function canChangeRole(User $actor, User $target): bool
    {
        if ($this->isAdministrator($actor)) {
            return true;
        }

        return $this->isCoordinator($actor)
            && !$this->isAdministrator($target)
            && !$this->isCoordinator($target);
    }

    public function canChangeStatus(User $actor, User $target): bool
    {
        return $actor->getKey() !== $target->getKey()
            && $this->canManageUser($actor, $target);
    }

    public function canResendWelcome(User $actor, User $target): bool
    {
        return $this->canManageUser($actor, $target)
            && $target->activo
            && !$target->hasVerifiedEmail();
    }

    public function canAssignRole(User $actor, Catalogo $role, ?User $target = null): bool
    {
        if (!$role->activo || $role->catalogo_id !== config('constantes.CATEGORIA_ROL_USUARIO')) {
            return false;
        }

        if ($target && !$this->canManageUser($actor, $target)) {
            return false;
        }

        if ($this->isAdministrator($actor)) {
            return true;
        }

        if (!$this->isCoordinator($actor)) {
            return false;
        }

        // Un Coordinador puede editar otros datos de un par, pero no reasignar su autoridad.
        if ($target && !$this->canChangeRole($actor, $target)) {
            return $role->id === $target->rol_id;
        }

        return !in_array($role->id, [
            config('constantes.ROL_COORDINADOR'),
            config('constantes.ROL_ADMINISTRADOR_IT'),
        ], true);
    }

    private function isAdministrator(User $user): bool
    {
        return $user->rol_id === config('constantes.ROL_ADMINISTRADOR_IT');
    }

    private function isCoordinator(User $user): bool
    {
        return $user->rol_id === config('constantes.ROL_COORDINADOR');
    }
}
