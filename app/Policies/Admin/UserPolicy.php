<?php

namespace App\Policies\Admin;

use App\Libraries\Helper;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * @param Admin $admin
     * @return bool
     */
    public function viewAny(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.user.index', $admin);
    }

    /**
     * @param Admin $admin
     * @param User $user
     * @return bool
     */
    public function view(Admin $admin, User $user): bool
    {
        return Helper::checkPermissionAdmin('admin.user.show', $admin, $user);
    }

    /**
     * @param Admin $admin
     * @return bool
     */
    public function create(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.user.create', $admin);
    }

    /**
     * @param Admin $admin
     * @param User $user
     * @return bool
     */
    public function update(Admin $admin, User $user): bool
    {
        return Helper::checkPermissionAdmin('admin.user.edit', $admin, $user);
    }

    /**
     * @param Admin $admin
     * @param User $user
     * @return bool
     */
    public function delete(Admin $admin, User $user): bool
    {
        return Helper::checkPermissionAdmin('admin.user.destroy', $admin, $user);
    }

    /**
     * @param Admin $admin
     * @return bool
     */
    public function download(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.user.download', $admin);
    }
}
