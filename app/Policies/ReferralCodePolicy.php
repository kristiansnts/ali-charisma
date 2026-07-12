<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use TomatoPHP\FilamentEcommerce\Models\ReferralCode;

class ReferralCodePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_referral::code');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ReferralCode $referralCode): bool
    {
        return $user->hasPermissionTo('view_referral::code');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_referral::code');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ReferralCode $referralCode): bool
    {
        return $user->hasPermissionTo('update_referral::code');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ReferralCode $referralCode): bool
    {
        return $user->hasPermissionTo('delete_referral::code');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_any_referral::code');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ReferralCode $referralCode): bool
    {
        return $user->hasPermissionTo('force_delete_referral::code');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->hasPermissionTo('force_delete_any_referral::code');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ReferralCode $referralCode): bool
    {
        return $user->hasPermissionTo('restore_referral::code');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->hasPermissionTo('restore_any_referral::code');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, ReferralCode $referralCode): bool
    {
        return $user->hasPermissionTo('replicate_referral::code');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->hasPermissionTo('reorder_referral::code');
    }
}
