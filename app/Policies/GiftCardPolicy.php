<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use TomatoPHP\FilamentEcommerce\Models\GiftCard;

class GiftCardPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_gift::card');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GiftCard $giftCard): bool
    {
        return $user->hasPermissionTo('view_gift::card');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_gift::card');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GiftCard $giftCard): bool
    {
        return $user->hasPermissionTo('update_gift::card');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GiftCard $giftCard): bool
    {
        return $user->hasPermissionTo('delete_gift::card');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_any_gift::card');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, GiftCard $giftCard): bool
    {
        return $user->hasPermissionTo('force_delete_gift::card');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->hasPermissionTo('force_delete_any_gift::card');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, GiftCard $giftCard): bool
    {
        return $user->hasPermissionTo('restore_gift::card');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->hasPermissionTo('restore_any_gift::card');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, GiftCard $giftCard): bool
    {
        return $user->hasPermissionTo('replicate_gift::card');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->hasPermissionTo('reorder_gift::card');
    }
}
