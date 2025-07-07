<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AttributeValue;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttributeValuePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_attribute::value');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AttributeValue $attributeValue): bool
    {
        return $user->can('view_attribute::value');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_attribute::value');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AttributeValue $attributeValue): bool
    {
        return $user->can('update_attribute::value');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AttributeValue $attributeValue): bool
    {
        return $user->can('delete_attribute::value');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_attribute::value');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, AttributeValue $attributeValue): bool
    {
        return $user->can('force_delete_attribute::value');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_attribute::value');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, AttributeValue $attributeValue): bool
    {
        return $user->can('restore_attribute::value');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_attribute::value');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, AttributeValue $attributeValue): bool
    {
        return $user->can('replicate_attribute::value');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_attribute::value');
    }
}
