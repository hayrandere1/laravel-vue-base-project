<?php

namespace App\Policies\Admin;

use App\Libraries\Helper;
use App\Models\Admin;
use App\Models\GuestDashboard;
use Illuminate\Auth\Access\Response;

class GuestDashboardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.company.index', $admin);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Admin $admin, GuestDashboard $guestDashboard): bool
    {
        return Helper::checkPermissionAdmin('admin.guest_dashboard.show', $admin, $guestDashboard);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.guest_dashboard.create', $admin);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Admin $admin, GuestDashboard $guestDashboard): bool
    {
        return Helper::checkPermissionAdmin('admin.guest_dashboard.edit', $admin, $guestDashboard);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin $admin, GuestDashboard $guestDashboard): bool
    {
        return Helper::checkPermissionAdmin('admin.guest_dashboard.destroy', $admin, $guestDashboard);
    }

    /**
     * @param Admin $admin
     * @return bool
     */
    public function download(Admin $admin): bool
    {
        return Helper::checkPermissionAdmin('admin.guest_dashboard.download', $admin);
    }
}
