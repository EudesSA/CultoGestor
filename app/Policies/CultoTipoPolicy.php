<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CultoTipo;
use Illuminate\Auth\Access\HandlesAuthorization;

class CultoTipoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CultoTipo');
    }

    public function view(AuthUser $authUser, CultoTipo $cultoTipo): bool
    {
        return $authUser->can('View:CultoTipo');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CultoTipo');
    }

    public function update(AuthUser $authUser, CultoTipo $cultoTipo): bool
    {
        return $authUser->can('Update:CultoTipo');
    }

    public function delete(AuthUser $authUser, CultoTipo $cultoTipo): bool
    {
        return $authUser->can('Delete:CultoTipo');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CultoTipo');
    }

    public function restore(AuthUser $authUser, CultoTipo $cultoTipo): bool
    {
        return $authUser->can('Restore:CultoTipo');
    }

    public function forceDelete(AuthUser $authUser, CultoTipo $cultoTipo): bool
    {
        return $authUser->can('ForceDelete:CultoTipo');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CultoTipo');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CultoTipo');
    }

    public function replicate(AuthUser $authUser, CultoTipo $cultoTipo): bool
    {
        return $authUser->can('Replicate:CultoTipo');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CultoTipo');
    }

}