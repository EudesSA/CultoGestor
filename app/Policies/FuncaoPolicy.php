<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Funcao;
use Illuminate\Auth\Access\HandlesAuthorization;

class FuncaoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Funcao');
    }

    public function view(AuthUser $authUser, Funcao $funcao): bool
    {
        return $authUser->can('View:Funcao');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Funcao');
    }

    public function update(AuthUser $authUser, Funcao $funcao): bool
    {
        return $authUser->can('Update:Funcao');
    }

    public function delete(AuthUser $authUser, Funcao $funcao): bool
    {
        return $authUser->can('Delete:Funcao');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Funcao');
    }

    public function restore(AuthUser $authUser, Funcao $funcao): bool
    {
        return $authUser->can('Restore:Funcao');
    }

    public function forceDelete(AuthUser $authUser, Funcao $funcao): bool
    {
        return $authUser->can('ForceDelete:Funcao');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Funcao');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Funcao');
    }

    public function replicate(AuthUser $authUser, Funcao $funcao): bool
    {
        return $authUser->can('Replicate:Funcao');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Funcao');
    }

}