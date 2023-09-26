<?php

namespace App\Policies\User;

use App\Libraries\Helper;
use App\Models\Person;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PersonPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return Helper::checkPermissionUser('user.person.index', $user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Person $person): bool
    {
        return Helper::checkPermissionUser('user.person.show', $user, $person);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Helper::checkPermissionUser('user.person.create', $user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Person $person): bool
    {
        return Helper::checkPermissionUser('user.person.edit', $user, $person);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Person $person): bool
    {
        return Helper::checkPermissionUser('user.person.destroy', $user, $person);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function download(User $user): bool
    {
        return Helper::checkPermissionUser('user.person.download', $user);
    }
}
