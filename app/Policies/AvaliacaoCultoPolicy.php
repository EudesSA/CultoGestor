<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AvaliacaoCulto;
use Illuminate\Auth\Access\HandlesAuthorization;

class AvaliacaoCultoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AvaliacaoCulto');
    }

    public function view(AuthUser $authUser, AvaliacaoCulto $avaliacaoCulto): bool
    {
        return $authUser->can('View:AvaliacaoCulto');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AvaliacaoCulto');
    }

    public function update(AuthUser $authUser, AvaliacaoCulto $avaliacaoCulto): bool
    {
        return $authUser->can('Update:AvaliacaoCulto');
    }

    public function delete(AuthUser $authUser, AvaliacaoCulto $avaliacaoCulto): bool
    {
        return $authUser->can('Delete:AvaliacaoCulto');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AvaliacaoCulto');
    }

    public function restore(AuthUser $authUser, AvaliacaoCulto $avaliacaoCulto): bool
    {
        return $authUser->can('Restore:AvaliacaoCulto');
    }

    public function forceDelete(AuthUser $authUser, AvaliacaoCulto $avaliacaoCulto): bool
    {
        return $authUser->can('ForceDelete:AvaliacaoCulto');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AvaliacaoCulto');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AvaliacaoCulto');
    }

    public function replicate(AuthUser $authUser, AvaliacaoCulto $avaliacaoCulto): bool
    {
        return $authUser->can('Replicate:AvaliacaoCulto');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AvaliacaoCulto');
    }

}