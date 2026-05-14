<?php

namespace Modules\Core\Policies\Vendor;

use Modules\Core\Models\Vendor;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-any-vendor');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vendor $vendor): bool
    {
        return $user->hasPermissionTo('view-vendor');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-vendor');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vendor $vendor): bool
    {
        return $user->hasPermissionTo('update-vendor');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vendor $vendor): bool
    {
        return $user->hasPermissionTo('delete-vendor');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Vendor $vendor): bool
    {
        return $user->hasPermissionTo('restore-vendor');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Vendor $vendor): bool
    {
        return $user->hasPermissionTo('force-delete-vendor');
    }
}
