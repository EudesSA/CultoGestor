<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Escala;
use Illuminate\Auth\Access\HandlesAuthorization;

class EscalaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Escala');
    }

    public function view(AuthUser $authUser, Escala $escala): bool
    {
        return $authUser->can('View:Escala');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Escala');
    }

    public function update(AuthUser $authUser, Escala $escala): bool
    {
        return $authUser->can('Update:Escala');
    }

    public function delete(AuthUser $authUser, Escala $escala): bool
    {
        return $authUser->can('Delete:Escala');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Escala');
    }

    public function restore(AuthUser $authUser, Escala $escala): bool
    {
        return $authUser->can('Restore:Escala');
    }

    public function forceDelete(AuthUser $authUser, Escala $escala): bool
    {
        return $authUser->can('ForceDelete:Escala');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Escala');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Escala');
    }

    public function replicate(AuthUser $authUser, Escala $escala): bool
    {
        return $authUser->can('Replicate:Escala');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Escala');
    }

}