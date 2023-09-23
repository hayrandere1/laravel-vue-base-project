<?php

namespace App\Policies\Manager;

use App\Libraries\Helper;
use App\Models\Manager;
use Illuminate\Auth\Access\Response;

class ManagerPolicy
{
    /**
     * @param Manager $manager
     * @return bool
     */
    public function viewAny(Manager $manager): bool
    {
        return Helper::checkPermissionManager('manager.manager.index', $manager);
    }

    /**
     * @param Manager $manager
     * @param Manager $model
     * @return bool
     */
    public function view(Manager $manager, Manager $model): bool
    {
        return Helper::checkPermissionManager('manager.manager.show', $manager, $model);
    }

    /**
     * @param Manager $manager
     * @return bool
     */
    public function create(Manager $manager): bool
    {
        return Helper::checkPermissionManager('manager.manager.create', $manager);
    }

    /**
     * @param Manager $manager
     * @param Manager $model
     * @return bool
     */
    public function update(Manager $manager, Manager $model): bool
    {
        return Helper::checkPermissionManager('manager.manager.edit', $manager, $model);
    }

    /**
     * @param Manager $manager
     * @param Manager $model
     * @return bool
     */
    public function delete(Manager $manager, Manager $model): bool
    {
        return Helper::checkPermissionManager('manager.manager.destroy', $manager, $model);
    }

    /**
     * @param Manager $manager
     * @return bool
     */
    public function download(Manager $manager):bool
    {
        return Helper::checkPermissionManager('manager.manager.download', $manager);
    }
}
