<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProvaiVedePlaylist;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProvaiVedePlaylistPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProvaiVedePlaylist');
    }

    public function view(AuthUser $authUser, ProvaiVedePlaylist $provaiVedePlaylist): bool
    {
        return $authUser->can('View:ProvaiVedePlaylist');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProvaiVedePlaylist');
    }

    public function update(AuthUser $authUser, ProvaiVedePlaylist $provaiVedePlaylist): bool
    {
        return $authUser->can('Update:ProvaiVedePlaylist');
    }

    public function delete(AuthUser $authUser, ProvaiVedePlaylist $provaiVedePlaylist): bool
    {
        return $authUser->can('Delete:ProvaiVedePlaylist');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProvaiVedePlaylist');
    }

    public function restore(AuthUser $authUser, ProvaiVedePlaylist $provaiVedePlaylist): bool
    {
        return $authUser->can('Restore:ProvaiVedePlaylist');
    }

    public function forceDelete(AuthUser $authUser, ProvaiVedePlaylist $provaiVedePlaylist): bool
    {
        return $authUser->can('ForceDelete:ProvaiVedePlaylist');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProvaiVedePlaylist');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProvaiVedePlaylist');
    }

    public function replicate(AuthUser $authUser, ProvaiVedePlaylist $provaiVedePlaylist): bool
    {
        return $authUser->can('Replicate:ProvaiVedePlaylist');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProvaiVedePlaylist');
    }

}