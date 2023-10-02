<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PackageRequest;
use App\Http\Resources\Admin\PackageResource;
use App\Libraries\Helper;
use App\Models\ManagerRole;
use App\Models\ManagerRoleGroup;
use App\Models\Package;
use App\Models\UserRole;
use App\Models\UserRoleGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PackageController extends Controller
{

    const PREFIX = 'admin.package';
    const PAGE = 'Admin/Package/Form';
    const PROCESS_NAME = self::PREFIX . '.processName';

    public function __construct()
    {
        $this->authorizeResource(Package::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortableColumns = [
            'id',
            'name',
            'company_count',
            'is_active',
            'created_at',
            'updated_at'];
        $limits = [10, 25, 50, 100];

        $filter = [
            'search' => $request->get('search', ''),
            'orderColumn' => $request->get('orderColumn', 'id'),
            'orderDirection' => $request->get('orderDirection', 'desc'),
            'limit' => (int)$request->get('limit', 10)
        ];
        $columns = [
            [
                'title' => 'ID',
                'align' => 'start',
                'key' => 'id'
            ],
            [
                'title' => 'Name',
                'align' => 'start',
                'key' => 'name'
            ],
            [
                'title' => 'Company Count',
                'align' => 'start',
                'key' => 'company_count'
            ],
            [
                'title' => 'Active',
                'align' => 'start',
                'key' => 'is_active'
            ],
            [
                'title' => 'Created At',
                'align' => 'start',
                'key' => 'created_at'
            ],
            [
                'title' => 'Updated At',
                'align' => 'start',
                'key' => 'updated_at'
            ],
            [
                'title' => 'Process',
                'align' => 'start',
                'key' => 'process',
                'sortable' => false,
                'width' => 250
            ],
        ];

        if (!in_array($filter['orderColumn'], $sortableColumns)) {
            $filter['sortColumn'] = 'id';
        }
        if (!in_array($filter['orderDirection'], ['asc', 'desc'])) {
            $filter['orderDirection'] = 'desc';
        }
        if (!in_array($filter['limit'], $limits)) {
            $filter['limit'] = 10;
        }

        $resource = [
            'prefix' => self::PREFIX,
            'columns' => $columns,
            'filter' => $filter,
        ];

        return Inertia::render('Admin/Package/List', compact('resource'));
    }

    /**
     * @param array $filter
     * @return Builder
     */
    private function getListQuery(array $filter): Builder
    {
        /* @var $query Builder */
        $query = Package::orderBy($filter['orderColumn'], $filter['orderDirection']);
        $query->leftJoin('companies', 'companies.package_id', '=', 'packages.id');
        $query->selectRaw('packages.*, count(companies.id) as company_count');
        $query->groupBy('packages.id');
        if (!empty($filter['search'])) {
            $query->where(function (Builder $query) use ($filter) {
                if (is_numeric($filter['search'])) {
                    $query->orWhere('packages.id', $filter['search']);
                }
                $query->orWhere('packages.name', 'like', '%' . $filter['search'] . '%');
                return $query;
            });
        }
        return $query;
    }


    public function getData(Request $request)
    {
        $sortableColumns = [
            'id',
            'name',
            'company_count',
            'is_active',
            'created_at',
            'updated_at'];
        $limits = [10, 25, 50, 100];

        $filter = [
            'search' => $request->get('search', ''),
            'orderColumn' => $request->get('orderColumn', 'id'),
            'orderDirection' => $request->get('orderDirection', 'desc'),
            'limit' => (int)$request->get('limit', 10)
        ];

        if (!in_array($filter['orderColumn'], $sortableColumns)) {
            $filter['sortColumn'] = 'id';
        }
        if (!in_array($filter['orderDirection'], ['asc', 'desc'])) {
            $filter['orderDirection'] = 'desc';
        }
        if (!in_array($filter['limit'], $limits)) {
            $filter['limit'] = 10;
        }

        $query = $this->getListQuery($filter);

        $recordsTotal = Package::count();

        $datas = $query->paginate($filter['limit'])->appends($filter);

        if ($datas->count() === 0 && $request->get('page', 1) > 1) {
            return redirect()->route('admin.package.index');
        }

        $resource = PackageResource::collection($datas)->additional([
            'recordsTotal' => $recordsTotal,
            'filter' => $filter,
        ]);
        return $resource;
    }


    public function download(Request $request): JsonResponse
    {
        $this->authorize('download', Package::class);

        $columns = [
            'ID' => 'id',
            'Name' => 'name',
            'Company Count' => 'company_count',
            'Is Active' => 'is_active',
            'Created At' => 'created_at'
        ];

        $filter = [
            'search' => $request->get('search', ''),
            'orderColumn' => 'id',
            'orderDirection' => 'desc'
        ];

        $query = $this->getListQuery($filter);
        $query2 = $this->getListQuery($filter);
        $datas = $query2->paginate(1);
        $filteredCount = $datas->total();
        if ($filteredCount > 0) {

            $fileName = 'package_file_name';
            if (!empty($request->search)) {
                $fileName .= '_' . $request->search;
            }

            $parameters = $query->getBindings();
            $sql = $query->toSql();
            if (Helper::generateArchiveObjectAndFile($sql, $parameters, $fileName, $filteredCount, $columns)) {
                return new JsonResponse(['process' => true, 'message' => 'started'], 200);
            } else {
                return new JsonResponse(['process' => true, 'message' => 'processing'], 200);
            }
        } else {
            return new JsonResponse(['process' => false, 'message' => 'noData'], 302);
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $managerRoles = ManagerRole::with('children')->where('parent', null)->get();
        $managerValues = [];


        foreach ($managerRoles as $mainRole) {
            foreach ($mainRole->children as $role) {
                if (!is_null($role->model) && !isset($managerValues[$role->model])) {
                    $managerValues[$role->model] = null;
                }
            }
        }

//        foreach ($managerRoles as $mainRole) {
//            foreach ($mainRole->children as $role) {
//                if (is_array(json_decode($role->model))) {
//                    $permissionTypes = [];
//                    $permissionValues = [];
//                    foreach (json_decode($role->model) as $model) {
//                        $permissionTypes[$model] = 'everyone';
//                        $permissionValues[$model] = [];
//                        if (!isset($values[$model])) {
//                            $relationName = Str::plural($model);
//                        }
//                    }
//                    $role->setAttribute('permissionTypes', $permissionTypes);
//                    $role->setAttribute('permissionValues', $permissionValues);
//                }
//            }
//        }

        $userRoles = UserRole::with('children')->where('parent', null)->get();
        $userValues = [];
        foreach ($userRoles as $mainRole) {
            foreach ($mainRole->children as $role) {
                if (!is_null($role->model) && !isset($userValues[$role->model])) {
                    $userValues[$role->model] = null;
                }
            }
        }

//        foreach ($userRoles as $mainRole) {
//            foreach ($mainRole->children as $role) {
//                if (is_array(json_decode($role->model))) {
//                    $permissionTypes = [];
//                    $permissionValues = [];
//                    foreach (json_decode($role->model) as $model) {
//                        $permissionTypes[$model] = 'everyone';
//                        $permissionValues[$model] = [];
//                        if (!isset($values[$model])) {
//                            $relationName = Str::plural($model);
//                        }
//                    }
//                    $role->setAttribute('permissionTypes', $permissionTypes);
//                    $role->setAttribute('permissionValues', $permissionValues);
//                }
//            }
//        }

        return Inertia::render(self::PAGE, compact('managerRoles', 'userRoles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PackageRequest $packageRequest)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        $loggerData = $packageRequest->validated();
        try {
            $managerMultipleRoles = $this->managerMultipleRoles();
            $userMultipleRoles = $this->userMultipleRoles();

            DB::beginTransaction();

            $managerRoleGroup = ManagerRoleGroup::create([
                'name' => 'Package Manager Role Group',
            ]);

            $userRoleGroup = UserRoleGroup::create([
                'name' => 'Package User Role Group',
            ]);

            $package = Package::create([
                'name' => $packageRequest->name,
                'manager_role_group_id' => $managerRoleGroup->id,
                'user_role_group_id' => $userRoleGroup->id
            ]);

            if ($packageRequest->has('managerRoles')) {
                foreach ($packageRequest->get('managerRoles') as $roles) {
                    foreach ($roles['children'] as $child) {
                        $filterValues = null;
                        $filterType = 'everyone';
                        if (@$child['checked']) {
                            if (isset($managerMultipleRoles[$child['id']])) {
                                $filterValues = $managerMultipleRoles[$child['id']];
                            }
                            $managerRoleGroup->roles()->attach($child['id'], [
                                'filter_type' => $filterType,
                                'filter_values' => (is_array($filterValues)) ? json_encode($filterValues) : $filterValues]);
                        }
                    }
                }
            }
            if ($packageRequest->has('userRoles')) {
                foreach ($packageRequest->get('userRoles') as $roles) {
                    foreach ($roles['children'] as $child) {
                        $filterValues = null;
                        $filterType = 'everyone';
                        if (@$child['checked']) {
                            if (isset($userMultipleRoles[$child['id']])) {
                                $filterValues = $userMultipleRoles[$child['id']];
                            }
                            $userRoleGroup->roles()->attach($child['id'], [
                                'filter_type' => $filterType,
                                'filter_values' => (is_array($filterValues)) ? json_encode($filterValues) : $filterValues]);
                        }
                    }
                }
            }
            DB::commit();
            return redirect()
                ->route('admin.package.index')
                ->with('message', __('global.messages.addSuccess', ['title' => __(self::PROCESS_NAME)]));
        } catch (QueryException $queryException) {
            $loggerData['error_code'] = $queryException->getCode();
            $loggerData['error_message'] = $queryException->getMessage();
            logger()->error($loggerPrefix . ' QueryException', $loggerData);
            report($queryException);
            try {
                DB::rollBack();
            } catch (\Throwable $throwable) {
                $loggerData['error_code'] = $throwable->getCode();
                $loggerData['error_message'] = $throwable->getMessage();
                logger()->critical($loggerPrefix . ' QueryException Rollback Throw', $loggerData);
                report($throwable);
            }
            $errorMessage = __('global.messages.addDbError', ['title' => __(self::PROCESS_NAME)]);

        } catch (\Throwable $throwable) {
            $loggerData['error_code'] = $throwable->getCode();
            $loggerData['error_message'] = $throwable->getMessage();
            logger()->error($loggerPrefix . ' Throwable', $loggerData);
            report($throwable);
            try {
                DB::rollBack();
            } catch (\Throwable $throwable) {
                $loggerData['error_code'] = $throwable->getCode();
                $loggerData['error_message'] = $throwable->getMessage();
                logger()->critical($loggerPrefix . ' Throwable Rollback Throw', $loggerData);
                report($throwable);
            }
            $errorMessage = __('global.messages.addUnknownError', ['title' => __(self::PROCESS_NAME)]);
        }

        return redirect()
            ->route('admin.package.create')
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        $userRoles = UserRole::with('children')->where('parent', null)->get();
        $managerRoles = ManagerRole::with('children')->where('parent', null)->get();
        $userRolesTemp = [];
        $managerRolesTemp = [];

        foreach ($package->userRoleGroup->roles as $role) {
            if (!is_null($role->parent)) {
                $userRolesTemp[] = $role->id;
            }
        }

        foreach ($package->managerRoleGroup->roles as $role) {
            if (!is_null($role->parent)) {
                $managerRolesTemp[] = $role->id;
            }
        }

        foreach ($managerRoles as $mainRole) {
            foreach ($mainRole->children as $role) {
                if (in_array($role->id, $managerRolesTemp)) {
                    $role->checked = true;
                }
            }
        }

        foreach ($userRoles as $mainRole) {
            foreach ($mainRole->children as $role) {
                if (in_array($role->id, $userRolesTemp)) {
                    $role->checked = true;
                }
            }
        }
        $show = true;

        return Inertia::render(self::PAGE,
            compact(
                'package',
                'managerRoles',
                'userRoles',
                'show'
            ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Package $package)
    {
        $userRoles = UserRole::with('children')->where('parent', null)->get();
        $managerRoles = ManagerRole::with('children')->where('parent', null)->get();
        $userRolesTemp = [];
        $managerRolesTemp = [];

        foreach ($package->userRoleGroup->roles as $role) {
            if (!is_null($role->parent)) {
                $userRolesTemp[] = $role->id;
            }
        }

        foreach ($package->managerRoleGroup->roles as $role) {
            if (!is_null($role->parent)) {
                $managerRolesTemp[] = $role->id;
            }
        }

        foreach ($managerRoles as $mainRole) {
            foreach ($mainRole->children as $role) {
                if (in_array($role->id, $managerRolesTemp)) {
                    $role->checked = true;
                }
            }
        }

        foreach ($userRoles as $mainRole) {
            foreach ($mainRole->children as $role) {
                if (in_array($role->id, $userRolesTemp)) {
                    $role->checked = true;
                }
            }
        }

        return Inertia::render(self::PAGE,
            compact(
                'package',
                'managerRoles',
                'userRoles',
            ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PackageRequest $packageRequest, Package $package)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        $loggerData = $packageRequest->validated();
        try {

            $managerMultipleRoles = $this->managerMultipleRoles();
            $userMultipleRoles = $this->userMultipleRoles();

            DB::beginTransaction();


            $package->update([
                'name' => $packageRequest->name,
            ]);
            $package->userRoleGroup->roles()->detach();
            $package->managerRoleGroup->roles()->detach();
            if ($packageRequest->has('managerRoles')) {
                foreach ($packageRequest->get('managerRoles') as $roles) {
                    foreach ($roles['children'] as $child) {
                        $filterValues = null;
                        $filterType = 'everyone';
                        if (@$child['checked']) {
                            if (isset($managerMultipleRoles[$child['id']])) {
                                $filterValues = $managerMultipleRoles[$child['id']];
                            }
                            $package->managerRoleGroup->roles()->attach($child['id'], [
                                'filter_type' => $filterType,
                                'filter_values' => (is_array($filterValues)) ? json_encode($filterValues) : $filterValues]);
                        }
                    }
                }
            }
            if ($packageRequest->has('userRoles')) {
                foreach ($packageRequest->get('userRoles') as $roles) {
                    foreach ($roles['children'] as $child) {
                        $filterValues = null;
                        $filterType = 'everyone';
                        if (@$child['checked']) {
                            if (isset($userMultipleRoles[$child['id']])) {
                                $filterValues = $userMultipleRoles[$child['id']];
                            }
                            $package->userRoleGroup->roles()->attach($child['id'], [
                                'filter_type' => $filterType,
                                'filter_values' => (is_array($filterValues)) ? json_encode($filterValues) : $filterValues]);
                        }
                    }
                }
            }
            DB::commit();
            return redirect()
                ->route('admin.package.index')
                ->with('message', __('global.messages.updateSuccess', ['title' => __(self::PROCESS_NAME)]));
        } catch (QueryException $queryException) {
            $loggerData['error_code'] = $queryException->getCode();
            $loggerData['error_message'] = $queryException->getMessage();
            logger()->error($loggerPrefix . ' QueryException', $loggerData);
            report($queryException);
            try {
                DB::rollBack();
            } catch (\Throwable $throwable) {
                $loggerData['error_code'] = $throwable->getCode();
                $loggerData['error_message'] = $throwable->getMessage();
                logger()->critical($loggerPrefix . ' QueryException Rollback Throw', $loggerData);
                report($throwable);
            }
            $errorMessage = __('global.messages.updateDbError', ['title' => __(self::PROCESS_NAME)]);

        } catch (\Throwable $throwable) {
            $loggerData['error_code'] = $throwable->getCode();
            $loggerData['error_message'] = $throwable->getMessage();
            logger()->error($loggerPrefix . ' Throwable', $loggerData);
            report($throwable);
            try {
                DB::rollBack();
            } catch (\Throwable $throwable) {
                $loggerData['error_code'] = $throwable->getCode();
                $loggerData['error_message'] = $throwable->getMessage();
                logger()->critical($loggerPrefix . ' Throwable Rollback Throw', $loggerData);
                report($throwable);
            }
            $errorMessage = __('global.messages.updateUnknownError', ['title' => __(self::PROCESS_NAME)]);
        }

        return redirect()
            ->route('admin.package.edit', $package)
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix, 'package_id' => $package->id]);
        try {
            $roleGroup = Package::find($package->id);
            $roleGroup->userRoleGroup->delete();
            $roleGroup->managerRoleGroup->delete();
            $roleGroup->delete();
            //            broadcast(new DeletedEvent(self::PREFIX, $userRoleGroup->id, 'company.' . Auth::user()->company_id, Auth::user()));
            return new JsonResponse(['process' => true, 'message' => __('global.messages.deleteSuccess', ['title' => __(self::PROCESS_NAME)])]);
        } catch (QueryException $queryException) {
            logger()->error($loggerPrefix . ' QueryException', [
                'error_code' => $queryException->getCode(),
                'error_message' => $queryException->getMessage()
            ]);
            if ($queryException->getCode() == 23000) {
                $errorMessage = __('global.messages.deleteDbErrorInUsed', ['title' => __(self::PROCESS_NAME)]);
            } else {
                $errorMessage = __('global.messages.deleteDbError', ['title' => __(self::PROCESS_NAME)]);
                report($queryException);
            }

            return new JsonResponse(['process' => false, 'message' => $errorMessage]);
        } catch (\Throwable $throwable) {
            logger()->error($loggerPrefix . ' Throwable', [
                'error_code' => $throwable->getCode(),
                'error_message' => $throwable->getMessage()
            ]);
            report($throwable);

            return new JsonResponse(['process' => false, 'message' => __('global.messages.deleteUnknownError', ['title' => __(self::PROCESS_NAME)])]);
        }
    }

    private function managerMultipleRoles(): array
    {
        $multipleRoles = [];
        $managerRoles = ManagerRole::whereNotNull('model')->get();
        foreach ($managerRoles as $managerRole) {
            if (!empty($managerRole->model)) {
                $models = json_decode($managerRoles->model);
                $filterValues = [];
                foreach ($models as $model) {
                    $filterValues[$model . '_filter_type'] = 'everyone';
                    $filterValues[$model] = [];
                }
                $multipleRoles[$managerRoles->id] = json_encode($filterValues);
            }
        }
        return $multipleRoles;
    }

    private function userMultipleRoles(): array
    {
        $multipleRoles = [];
        $userRoles = UserRole::whereNotNull('model')->get();
        foreach ($userRoles as $userRole) {
            if (!empty($userRole->model)) {
                $models = json_decode($userRole->model);
                $filterValues = [];
                foreach ($models as $model) {
                    $filterValues[$model . '_filter_type'] = 'everyone';
                    $filterValues[$model] = [];
                }
                $multipleRoles[$userRole->id] = json_encode($filterValues);
            }
        }
        return $multipleRoles;
    }

}
