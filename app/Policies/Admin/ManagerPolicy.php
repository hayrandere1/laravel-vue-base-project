<?php

namespace App\Policies\Admin;

use App\Libraries\Helper;
use App\Models\Admin;
use App\Models\Manager;

class ManagerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.manager.index', $admin);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Admin $admin, Manager $manager): bool
    {
        return Helper::checkPermissionAdmin('admin.manager.show', $admin, $manager);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.manager.create', $admin);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Admin $admin, Manager $manager): bool
    {
        return Helper::checkPermissionAdmin('admin.manager.edit', $admin, $manager);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin $admin, Manager $manager): bool
    {
        return Helper::checkPermissionAdmin('admin.manager.destroy', $admin, $manager);
    }

    /**
     * @param Admin $admin
     * @return bool
     */
    public function download(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.manager.download', $admin);
    }
}
