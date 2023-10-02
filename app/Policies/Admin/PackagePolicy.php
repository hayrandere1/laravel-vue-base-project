<?php

namespace App\Policies\Admin;

use App\Libraries\Helper;
use App\Models\Admin;
use App\Models\Package;
use Illuminate\Auth\Access\Response;

class PackagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.package.index', $admin);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Admin $admin, Package $package): bool
    {
        return Helper::checkPermissionAdmin('admin.package.show', $admin, $package);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.package.create', $admin);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Admin $admin, Package $package): bool
    {
        return Helper::checkPermissionAdmin('admin.package.edit', $admin, $package);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin $admin, Package $package): bool
    {
        return Helper::checkPermissionAdmin('admin.package.destroy', $admin, $package);
    }

    /**
     * @param Admin $admin
     * @return bool
     */
    public function download(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.package.download', $admin);
    }
}
