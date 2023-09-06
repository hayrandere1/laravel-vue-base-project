<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRoleGroupRequest;
use App\Http\Resources\Admin\AdminRoleGroupResource;
use App\Models\AdminRole;
use App\Models\AdminRoleGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminRoleGroupController extends Controller
{

    const PREFIX = 'admin.admin_role_group';
    const PAGE = 'Admin/RoleGroup/Form';
    const PROCESS_NAME = self::PREFIX . '.processName';

    public function __construct()
    {
        $this->authorizeResource(AdminRoleGroup::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortableColumns = ['id', 'name', 'admin_count', 'created_at', 'updated_at'];
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
                'title' => 'Admin Count',
                'align' => 'start',
                'key' => 'admin_count'
            ],
            [
                'title' => 'Created At',
                'align' => 'start',
                'key' => 'created_at'
            ],
            [
                'title' => 'Process',
                'align' => 'start',
                'key' => 'process',
                'sortable' => false,
            ],
        ];

        $adminColumns = [
            [
                'title' => 'ID',
                'align' => 'start',
                'key' => 'id'
            ],
            [
                'title' => 'Username',
                'align' => 'start',
                'key' => 'username'
            ],
            [
                'title' => 'First Name',
                'align' => 'start',
                'key' => 'first_name'
            ],
            [
                'title' => 'Last Name',
                'align' => 'start',
                'key' => 'last_name'
            ],
            [
                'title' => 'Email',
                'align' => 'start',
                'key' => 'email'
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
            'adminColumns' => $adminColumns,
        ];
//        $resource = AdminRoleGroupResource::collection($datas)->additional([
//            'recordsTotal' => $recordsTotal,
//            'sortableColumns' => $sortableColumns,
//            'columns' => $columns,
//            'filter' => $filter,
//            'limits' => array_combine($limits, $limits),
//            'prefix' => self::PREFIX
//        ]);

        return Inertia::render("Admin/RoleGroup/List", compact('resource'));


    }


    /**
     * @param array $filter
     * @return Builder
     */
    private function getListQuery(array $filter): Builder
    {
        if ($filter['orderColumn'] != 'admin_count') {
            $filter['orderColumn'] = 'admin_role_groups.' . $filter['orderColumn'];
        }
        /* @var $query Builder */
        $query = AdminRoleGroup::orderBy($filter['orderColumn'], $filter['orderDirection']);

        $query->leftJoin('admins', 'admins.role_group_id', '=', 'admin_role_groups.id');
        $query->selectRaw('admin_role_groups.*, count(admins.id) as admin_count');
        $query->groupBy([
            'admin_role_groups.id',
            'admin_role_groups.name',
            'admin_role_groups.created_at',
            'admin_role_groups.updated_at'
        ]);

        if (!empty($filter['search'])) {
            $query->where(function (Builder $query) use ($filter) {
                if (is_numeric($filter['search'])) {
                    $query->orWhere('admin_role_groups.id', $filter['search']);
                }
                $query->orWhere('admin_role_groups.name', 'like', '%' . $filter['search'] . '%');
                return $query;
            });
        }
        return $query;
    }


    public function getData(Request $request)
    {
        $sortableColumns = ['id', 'name', 'admin_count', 'created_at', 'updated_at'];
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

        $recordsTotal = AdminRoleGroup::count();

        $datas = $query->paginate($filter['limit'])->appends($filter);

        if ($datas->count() === 0 && $request->get('page', 1) > 1) {
            return redirect()->route('admin.admin_role_group.index');
        }

        $resource = AdminRoleGroupResource::collection($datas)->additional([
            'recordsTotal' => $recordsTotal,
            'filter' => $filter,
        ]);
        return $resource;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $adminRoles = AdminRole::with('children')->where('parent', null)->get();
        $values = [];

        foreach ($adminRoles as $mainRole) {
            foreach ($mainRole->children as $role) {
                if (is_array(json_decode($role->model))) {
                    $permissionTypes = [];
                    $permissionValues = [];
                    foreach (json_decode($role->model) as $model) {
                        $permissionTypes[$model] = 'everyone';
                        $permissionValues[$model] = [];
                        if (!isset($values[$model])) {
                            $relationName = Str::plural($model);
                            dd($relationName);
                        }
                    }
                    $role->setAttribute('permissionTypes', $permissionTypes);
                    $role->setAttribute('permissionValues', $permissionValues);
                }
            }
        }


        return Inertia::render(self::PAGE, compact('adminRoles', 'values'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRoleGroupRequest $adminRoleGroupRequest)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        $loggerData = $adminRoleGroupRequest->validated();
        try {
            $multipleRoles = $this->multipleRoles();
            DB::beginTransaction();
            $adminRoleGroup = AdminRoleGroup::create([
                'name' => $adminRoleGroupRequest->name,
            ]);
            if ($adminRoleGroupRequest->has('adminRoles')) {
                foreach ($adminRoleGroupRequest->get('adminRoles') as $roles) {
                    foreach ($roles['children'] as $child) {
                        $filterValues = null;
                        $filterType = 'everyone';
                        if (@$child['checked']) {
                            if (isset($multipleRoles[$child['id']])) {
                                $filterType = 'multiple';
                                foreach ($multipleRoles[$child['id']] as $model) {
                                    $filterValues[$model . '_filter_type'] = $child['permissionTypes'][$model];
                                    $filterValues[$model] = @$child['permissionValues'][$model];
                                }
                            }
                            $adminRoleGroup->roles()->attach($child['id'], [
                                'filter_type' => $filterType,
                                'filter_values' => (is_array($filterValues)) ? json_encode($filterValues) : $filterValues]);
                        }
                    }
                }
            }
            DB::commit();
            // @Todo: created event
            return redirect()
                ->route('admin.admin_role_group.index')
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
            ->route('admin.admin_role_group.create')
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Display the specified resource.
     */
    public function show(AdminRoleGroup $adminRoleGroup)
    {
        $show = true;
        $adminRoles = AdminRole::with('children')->where('parent', null)->get();

        $values = [];
        $roles = [];
        $permissionTypes = [];
        $permissionValues = [];
        foreach ($adminRoleGroup->roles as $role) {
            if (!is_null($role->parent)) {
                $roles[] = $role->id;
                if (!empty($role->model)) {
                    foreach (json_decode($role->model) as $model) {
                        $filterTypeName = $model . '_filter_type';
                        if (!empty($role->pivot->filter_values)) {
                            $permissionTypes[$role->id . '_' . $model] = json_decode($role->pivot->filter_values)->$filterTypeName;
                            try {
                                $permissionValues[$role->id . '_' . $model] = json_decode($role->pivot->filter_values)->$model;
                            } catch (\Throwable) {
//                                dd( json_decode($role->pivot->filter_values)->$model,$model);
                            }
                        } else {
                            $permissionTypes[$role->id . '_' . $model] = 'everyone';
                            $permissionValues[$role->id . '_' . $model] = [];
                        }
                    }
                }
            }
        }

        foreach ($adminRoles as $mainRole) {
            foreach ($mainRole->children as $role) {
                if (in_array($role->id, $roles)) {
                    $role->checked = true;
                }

                $tempTypes = [];
                $tempValues = [];
                if (is_array(json_decode($role->model))) {
                    foreach (json_decode($role->model) as $model) {
                        if (!empty($permissionTypes[$role->id . '_' . $model])) {
                            $tempTypes[$model] = $permissionTypes[$role->id . '_' . $model];
                            $tempValues[$model] = $permissionValues[$role->id . '_' . $model];
                        }
                        if (!isset($values[$model])) {
                            $relationName = Str::plural($model);
                            $values[$model] = Auth::user()->company->$relationName;
                        }
                    }
                }
                if (empty($role->permissionTypes)) {
                    $role->setAttribute('permissionTypes', $tempTypes);
                    $role->setAttribute('permissionValues', $tempValues);
                }
            }
        }
        $roleGroup = $adminRoleGroup;
        $roleGroup['rolesIds'] = $roles;
        return Inertia::render(self::PAGE, compact(
            'roleGroup',
            'adminRoles',
            'values',
            'permissionTypes',
            'permissionValues',
            'show'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdminRoleGroup $adminRoleGroup)
    {
        $adminRoles = AdminRole::with('children')->where('parent', null)->get();

        $values = [];
        $roles = [];
        $permissionTypes = [];
        $permissionValues = [];
        foreach ($adminRoleGroup->roles as $role) {
            if (!is_null($role->parent)) {
                $roles[] = $role->id;
                if (!empty($role->model)) {
                    foreach (json_decode($role->model) as $model) {
                        $filterTypeName = $model . '_filter_type';
                        if (!empty($role->pivot->filter_values)) {
                            $permissionTypes[$role->id . '_' . $model] = json_decode($role->pivot->filter_values)->$filterTypeName;
                            try {
                                $permissionValues[$role->id . '_' . $model] = json_decode($role->pivot->filter_values)->$model;
                            } catch (\Throwable) {
//                                dd( json_decode($role->pivot->filter_values)->$model,$model);
                            }
                        } else {
                            $permissionTypes[$role->id . '_' . $model] = 'everyone';
                            $permissionValues[$role->id . '_' . $model] = [];
                        }
                    }
                }
            }
        }

        foreach ($adminRoles as $mainRole) {
            foreach ($mainRole->children as $role) {
                if (in_array($role->id, $roles)) {
                    $role->checked = true;
                }

                $tempTypes = [];
                $tempValues = [];
                if (is_array(json_decode($role->model))) {
                    foreach (json_decode($role->model) as $model) {
                        if (!empty($permissionTypes[$role->id . '_' . $model])) {
                            $tempTypes[$model] = $permissionTypes[$role->id . '_' . $model];
                            $tempValues[$model] = $permissionValues[$role->id . '_' . $model];
                        }
                        if (!isset($values[$model])) {
                            $relationName = Str::plural($model);
                            $values[$model] = Auth::user()->company->$relationName;
                        }
                    }
                }
                if (empty($role->permissionTypes)) {
                    $role->setAttribute('permissionTypes', $tempTypes);
                    $role->setAttribute('permissionValues', $tempValues);
                }
            }
        }
        $roleGroup = $adminRoleGroup;
        $roleGroup['rolesIds'] = $roles;
        return Inertia::render(self::PAGE,
            compact('roleGroup',
                'adminRoles',
                'values',
                'permissionTypes',
                'permissionValues'
            ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRoleGroupRequest $adminRoleGroupRequest, AdminRoleGroup $adminRoleGroup)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        $loggerData = $adminRoleGroupRequest->validated();
        try {
            $multipleRoles = $this->multipleRoles();
            DB::beginTransaction();
            $adminRoleGroup->update([
                'name' => $adminRoleGroupRequest->name
            ]);
            $adminRoleGroup->roles()->detach();
            if ($adminRoleGroupRequest->has('adminRoles')) {
                foreach ($adminRoleGroupRequest->get('adminRoles') as $roles) {
                    foreach ($roles['children'] as $child) {
                        $filterValues = null;
                        $filterType = 'everyone';
                        if (@$child['checked']) {
                            if (isset($multipleRoles[$child['id']])) {
                                $filterType = 'multiple';
                                foreach ($multipleRoles[$child['id']] as $model) {
                                    $filterValues[$model . '_filter_type'] = $child['permissionTypes'][$model];
                                    $filterValues[$model] = @$child['permissionValues'][$model];
                                }
                            }
                            $adminRoleGroup->roles()->attach($child['id'], [
                                'filter_type' => $filterType,
                                'filter_values' => (is_array($filterValues)) ? json_encode($filterValues) : $filterValues]);
                        }
                    }
                }
            }
            DB::commit();
            // @Todo: created event
            return redirect()
                ->route('admin.admin_role_group.index')
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
            ->route('admin.admin_role_group.edit', $adminRoleGroup)
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * @param AdminRoleGroup $adminRoleGroup
     * @return JsonResponse
     */
    public function destroy(AdminRoleGroup $adminRoleGroup):JsonResponse
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix, 'admin_role_group_id' => $adminRoleGroup->id]);
        try {
            $roleGroup = AdminRoleGroup::find($adminRoleGroup->id);
            $roleGroup->delete();
            //            broadcast(new DeletedEvent(self::PREFIX, $userRoleGroup->id, 'company.' . Auth::user()->company_id, Auth::user()));
            return new JsonResponse(['process' => true, 'message' => __('global.messages.deleteSuccess', ['title' => __(self::PROCESS_NAME)])]);

//            return redirect()->route('admin.admin_role_group.index')
//                ->with('message', __('global.messages.deleteSuccess', ['title' => __(self::PROCESS_NAME)]));
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
//            return redirect()->route('admin.admin_role_group.index')
//                ->with('error', $errorMessage);
        } catch (\Throwable $throwable) {
            logger()->error($loggerPrefix . ' Throwable', [
                'error_code' => $throwable->getCode(),
                'error_message' => $throwable->getMessage()
            ]);
            report($throwable);

            return new JsonResponse(['process' => false, 'message' =>  __('global.messages.deleteUnknownError', ['title' => __(self::PROCESS_NAME)])]);
//            return redirect()->route('admin.admin_role_group.index')
//                ->with('error', __('global.messages.deleteUnknownError', ['title' => __(self::PROCESS_NAME)]));
        }
    }

    /**
     * @return array
     */
    private function multipleRoles(): array
    {
        $multipleRoles = [];
        $adminRoles = AdminRole::whereNotNull('model')->get();
        foreach ($adminRoles as $adminRole) {
            if (!empty($adminRole->model)) {
                $multipleRoles[$adminRole->id] = json_decode($adminRole->model);
            }
        }
        return $multipleRoles;
    }
}
