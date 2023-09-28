<?php

namespace App\Libraries;

use App\Jobs\GenerateCsvJob;
use App\Models\Admin;
use App\Models\Archive;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;

class Helper
{
    /**
     * @param $routeName
     * @param Authenticatable $user
     * @return string
     */
    public static function getFilterType($routeName, Authenticatable $user)
    {
        if (is_null($user->roleGroup)) {
            return 'everyone';
        }
        return app('userRoles')->getRoles($user)[$routeName]->pivot->filter_type;
    }

    /**
     * @param $routeName
     * @param Authenticatable $user
     * @return array
     */
    public static function getFilterValues($routeName, Authenticatable $user): array
    {
        return explode(',', app('userRoles')->getRoles($user)[$routeName]->pivot->filter_values);
    }

    /**
     * @param $routeName
     * @param Admin $admin
     * @param Model|null $model
     * @param Request|null $request
     * @return bool
     */
    public static function checkPermissionAdmin($routeName, Admin $admin, Model $model = null, Request $request = null)
    {
        if (array_key_exists($routeName, app('userRoles')->getRoles($admin))) {
            if (is_null($model) && is_null($request)) {
                return true;
            }
            $filterType = self::getFilterType($routeName, $admin);
            if ($filterType == 'everyone') {
                return true;
            } elseif ($filterType == 'only_selected_values') {
                $filterValues = array_flip(self::getFilterValues($routeName, $admin));
                $key = app('userRoles')->getRoles($admin)[$routeName]->model . '_id';
                if (!is_null($request)) {
                    return array_key_exists($request->$key, $filterValues);
                }
                return array_key_exists($model->$key, $filterValues);
            } elseif ($filterType == 'only_me') {
                if (!is_null($request)) {
                    return $request->admin_id == $admin->id;
                }
                return $admin->id == $model->admin_id;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * @param $routeName
     * @param Manager $manager
     * @param Model|null $model
     * @param Request|null $request
     * @return bool
     */
    public static function checkPermissionManager($routeName, Manager $manager, Model $model = null, Request $request = null): bool
    {
        if (array_key_exists($routeName, app('userRoles')->getRoles($manager))) {
            if (is_null($model) && is_null($request)) {
                return true;
            }
            $filterType = self::getFilterType($routeName, $manager);
            if ($filterType == 'everyone') {
                return true;
            } elseif ($filterType == 'only_selected_values') {
                $filterValues = array_flip(self::getFilterValues($routeName, $manager));
                $key = app('userRoles')->getRoles($manager)[$routeName]->model . '_id';
                if (!is_null($request)) {
                    return array_key_exists($request->$key, $filterValues);
                }
                return array_key_exists($model->$key, $filterValues);
            } elseif ($filterType == 'only_me') {
                if (!is_null($request)) {
                    return $request->manager_id == $manager->id;
                }
                return $manager->id == $model->manager_id;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * @param $routeName
     * @param User $user
     * @param Model|null $model
     * @param Request|null $request
     * @return bool
     */
    public static function checkPermissionUser($routeName, User $user, Model $model = null, Request $request = null): bool
    {
        if (array_key_exists($routeName, app('userRoles')->getRoles($user))) {
            if (is_null($model) && is_null($request)) {
                return true;
            }
            $filterType = self::getFilterType($routeName, $user);
            if ($filterType == 'everyone') {
                return true;
            } elseif ($filterType == 'only_selected_values') {
                $filterValues = array_flip(self::getFilterValues($routeName, $user));
                $key = app('userRoles')->getRoles($user)[$routeName]->model . '_id';
                if (!is_null($request)) {
                    return array_key_exists($request->$key, $filterValues);
                }
                return array_key_exists($model->$key, $filterValues);
            } elseif ($filterType == 'only_me') {
                if (!is_null($request)) {
                    return $request->user_id == $user->id;
                }
                return $user->id == $model->user_id;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * @param $sql
     * @param $parameters
     * @param $fileName
     * @param $totalCount
     * @param $columns
     * @return bool
     */
    public static function generateArchiveObjectAndFile($sql, $parameters, $fileName, $totalCount, $columns): bool
    {

        $mdUniqueFileName = md5(auth()->user()->id . $fileName . json_encode($parameters));
        $mdUnique = md5(date('Y-m-d H:i:s') . auth()->user()->id . $fileName . json_encode($parameters));
        $duplicateValue = Archive::where('unique_file_name_id', $mdUniqueFileName)
            ->where('status', '!=', 'Finished')
            ->first();

        if (is_null($duplicateValue)) {
            $isAdmin = str_starts_with(\request()->getRequestUri(), '/Admin');
            $isManager = str_starts_with(\request()->getRequestUri(), '/Manager');
            $archive = Archive::create([
                'user_id' => $isAdmin ? auth('admin')->user()->id : (($isManager) ? auth('manager')->user()->id : auth()->user()->id),
                'sql' => $sql, //model
                'parameters' => json_encode($parameters), //condition
                'status' => 'Pending',
                'file_name' => $fileName,
                'unique_file_name_id' => $mdUniqueFileName,
                'unique_id' => $mdUnique,
                'total_count' => $totalCount,
                'completed_count' => 0,
                'columns' => json_encode($columns),
                'language' => app()->getLocale(),
                'type' => $isAdmin ? 'admin' : (($isManager) ? 'manager' : 'user')
            ]);

            GenerateCsvJob::dispatch($archive)->onQueue('csv_generate');

            return true;
        } else {
            return false;
        }
    }


}
