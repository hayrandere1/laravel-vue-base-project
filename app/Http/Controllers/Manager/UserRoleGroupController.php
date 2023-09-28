<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\UserRoleGroupRequest;
use App\Http\Resources\Manager\UserRoleGroupResource;
use App\Libraries\Helper;
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
use Inertia\Response;

class UserRoleGroupController extends Controller
{
    const PREFIX = 'manager.user_role_group';
    const PAGE = 'Manager/UserRoleGroup/Form';
    const PROCESS_NAME = self::PREFIX . '.processName';

    public function __construct()
    {
        $this->authorizeResource(UserRoleGroup::class);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortableColumns = ['id', 'name', 'user_count', 'created_at', 'updated_at'];
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
                'title' => 'User Count',
                'align' => 'start',
                'key' => 'user_count'
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

        $userColumns = [
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
            'userColumns' => $userColumns,
        ];

        return Inertia::render("Manager/UserRoleGroup/List", compact('resource'));


    }

    /**
     * @param array $filter
     * @return Builder
     */
    private function getListQuery(array $filter): Builder
    {
        if ($filter['orderColumn'] != 'user_count') {
            $filter['orderColumn'] = 'user_role_groups.' . $filter['orderColumn'];
        }
        /* @var $query Builder */
        $query = UserRoleGroup::orderBy($filter['orderColumn'], $filter['orderDirection']);

        $query->leftJoin('users', 'users.role_group_id', '=', 'user_role_groups.id');
        $query->selectRaw('user_role_groups.*, count(users.id) as user_count');
        $query->groupBy([
            'user_role_groups.id',
            'user_role_groups.name',
            'user_role_groups.created_at',
            'user_role_groups.updated_at'
        ]);

        if (!empty($filter['search'])) {
            $query->where(function (Builder $query) use ($filter) {
                if (is_numeric($filter['search'])) {
                    $query->orWhere('user_role_groups.id', $filter['search']);
                }
                $query->orWhere('user_role_groups.name', 'like', '%' . $filter['search'] . '%');
                return $query;
            });
        }
        return $query;
    }


    public function getData(Request $request)
    {
        $sortableColumns = ['id', 'name', 'user_count', 'created_at', 'updated_at'];
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

        $recordsTotal = UserRoleGroup::count();

        $datas = $query->paginate($filter['limit'])->appends($filter);

        if ($datas->count() === 0 && $request->get('page', 1) > 1) {
            return redirect()->route('manager.user_role_group.index');
        }

        $resource = UserRoleGroupResource::collection($datas)->additional([
            'recordsTotal' => $recordsTotal,
            'filter' => $filter,
        ]);
        return $resource;
    }

    public function download(Request $request):JsonResponse
    {
        $this->authorize('download', UserRoleGroup::class);

        $columns = [
            'ID' => 'id',
            'Name' => 'name',
            'User Count'=>'user_count',
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

            $fileName = 'user_role_group_file_name';
            if (!empty($request->search)) {
                $fileName .= '_' . $request->search;
            }

            $parameters = $query->getBindings();
            $sql = $query->toSql();
            if (Helper::generateArchiveObjectAndFile($sql, $parameters, $fileName, $filteredCount, $columns)) {
                return new JsonResponse(['process' => true, 'message' => 'started'],200);
            } else {
                return new JsonResponse(['process' => true, 'message' => 'processing'],200);
            }
        } else {
            return new JsonResponse(['process' => false, 'message' => 'noData'],302);
        }
    }


    /**
     * @return \Inertia\Response
     */
    public function create(): Response
    {
        $userRoles = UserRole::with('children')->where('parent', null)->get();
        $values = [];

        foreach ($userRoles as $mainRole) {
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

        return Inertia::render(self::PAGE, compact('userRoles', 'values'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRoleGroupRequest $userRoleGroupRequest)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        $loggerData = $userRoleGroupRequest->validated();
        try {
            $multipleRoles = $this->multipleRoles();
            DB::beginTransaction();
            $userRoleGroup = Auth::user()->company->userRoleGroups()->create([
                'name' => $userRoleGroupRequest->name,
            ]);
            if ($userRoleGroupRequest->has('userRoles')) {
                foreach ($userRoleGroupRequest->get('userRoles') as $roles) {
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
                            $userRoleGroup->roles()->attach($child['id'], [
                                'filter_type' => $filterType,
                                'filter_values' => (is_array($filterValues)) ? json_encode($filterValues) : $filterValues]);
                        }
                    }
                }
            }
            DB::commit();
            // @Todo: created event
            return redirect()
                ->route('manager.user_role_group.index')
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
            ->route('manager.user_role_group.create')
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserRoleGroup $userRoleGroup)
    {
        $show = true;
        $userRoles = UserRole::with('children')->where('parent', null)->get();

        $values = [];
        $roles = [];
        $permissionTypes = [];
        $permissionValues = [];
        foreach ($userRoleGroup->roles as $role) {
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

        foreach ($userRoles as $mainRole) {
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
        $roleGroup = $userRoleGroup;
        $roleGroup['rolesIds'] = $roles;
        return Inertia::render(self::PAGE, compact(
            'roleGroup',
            'userRoles',
            'values',
            'permissionTypes',
            'permissionValues',
            'show'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserRoleGroup $userRoleGroup)
    {
        $userRoles = UserRole::with('children')->where('parent', null)->get();

        $values = [];
        $roles = [];
        $permissionTypes = [];
        $permissionValues = [];
        foreach ($userRoleGroup->roles as $role) {
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

        foreach ($userRoles as $mainRole) {
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
        $roleGroup = $userRoleGroup;
        $roleGroup['rolesIds'] = $roles;
        return Inertia::render(self::PAGE,
            compact('roleGroup',
                'userRoles',
                'values',
                'permissionTypes',
                'permissionValues'
            ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRoleGroupRequest $userRoleGroupRequest, UserRoleGroup $userRoleGroup)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        $loggerData = $userRoleGroupRequest->validated();
        try {
            $multipleRoles = $this->multipleRoles();
            DB::beginTransaction();
            $userRoleGroup->update([
                'name' => $userRoleGroupRequest->name
            ]);
            $userRoleGroup->roles()->detach();
            if ($userRoleGroupRequest->has('userRoles')) {
                foreach ($userRoleGroupRequest->get('userRoles') as $roles) {
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
                            $userRoleGroup->roles()->attach($child['id'], [
                                'filter_type' => $filterType,
                                'filter_values' => (is_array($filterValues)) ? json_encode($filterValues) : $filterValues]);
                        }
                    }
                }
            }
            DB::commit();
            // @Todo: created event
            return redirect()
                ->route('manager.user_role_group.index')
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
            ->route('manager.user_role_group.edit', $userRoleGroup)
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserRoleGroup $userRoleGroup)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix, 'user_role_group_id' => $userRoleGroup->id]);
        try {
            $roleGroup = UserRoleGroup::find($userRoleGroup->id);
            $roleGroup->delete();
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

    private function multipleRoles(): array
    {
        $multipleRoles = [];
        $userRoles = UserRole::whereNotNull('model')->get();
        foreach ($userRoles as $userRole) {
            if (!empty($userRole->model)) {
                $multipleRoles[$userRole->id] = json_decode($userRole->model);
            }
        }
        return $multipleRoles;
    }
}
