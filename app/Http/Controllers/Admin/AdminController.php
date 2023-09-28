<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use App\Http\Resources\Admin\AdminResource;
use App\Libraries\Helper;
use App\Models\Admin;
use App\Models\AdminRoleGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Symfony\Component\Mailer\Exception\TransportException;

class AdminController extends Controller
{

    const PREFIX = 'admin.admin';
    const PAGE = 'Admin/Admin/Form';
    const PROCESS_NAME = self::PREFIX . '.processName';

    public function __construct()
    {
        $this->authorizeResource(Admin::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortableColumns = [
            'id',
            'role_group',
            'username',
            'first_name',
            'last_name',
            'email',
            'is_active',
            'created_at',
            'updated_at'];
        $limits = [10, 25, 50, 100];

        $filter = [
            'search' => $request->get('search', ''),
            'role_group_id' => $request->get('role_group_id', ''),
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
                'title' => 'Role Group',
                'align' => 'start',
                'key' => 'role_group'
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

        return Inertia::render('Admin/Admin/List', compact('resource'));
    }

    /**
     * @param array $filter
     * @return Builder
     */
    private function getListQuery(array $filter): Builder
    {
        /* @var $query Builder */
        $query = Admin::orderBy($filter['orderColumn'], $filter['orderDirection']);
        $query->leftJoin('admin_role_groups', 'admins.role_group_id', '=', 'admin_role_groups.id');
        $query->selectRaw('admins.*, admin_role_groups.name as role_group');

        if (!empty($filter['search'])) {
            $query->where(function (Builder $query) use ($filter) {
                if (is_numeric($filter['search'])) {
                    $query->orWhere('admins.id', $filter['search']);
                }
                $query->orWhere('admins.username', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('admins.first_name', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('admins.last_name', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('admins.email', 'like', '%' . $filter['search'] . '%');
                return $query;
            });
        }
        return $query;
    }


    public function getData(Request $request)
    {
        $sortableColumns = [
            'id',
            'role_group',
            'username',
            'first_name',
            'last_name',
            'email',
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

        $recordsTotal = Admin::count();

        $datas = $query->paginate($filter['limit'])->appends($filter);

        if ($datas->count() === 0 && $request->get('page', 1) > 1) {
            return redirect()->route('admin.admin.index');
        }

        $resource = AdminResource::collection($datas)->additional([
            'recordsTotal' => $recordsTotal,
            'filter' => $filter,
        ]);
        return $resource;
    }


    public function download(Request $request):JsonResponse
    {
        $this->authorize('download', Admin::class);

        $columns = [
            'ID' => 'id',
            'Role Group'=>'role_group',
            'Username'=>'username',
            'First Name'=>'first_name',
            'Last Name'=>'last_name',
            'Email'=>'email',
            'Is Active'=>'is_active',
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

            $fileName = 'admin_file_name';
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $adminRoleGroups = AdminRoleGroup::all();
        return Inertia::render(self::PAGE, compact('adminRoleGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $adminRequest)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        try {
            $password = rand(100000, 999999);
            $dbPassword = Hash::make($password);
            $validatedData = $adminRequest->validated();
            $validatedData['password'] = $dbPassword;
            $loggerData = $validatedData;
            DB::beginTransaction();
            $admin = Admin::create($validatedData);

            $admin->sendPasswordNotification($admin->username, $password);
            DB::commit();
            return redirect()
                ->route('admin.admin.index')
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

        } catch (TransportException $transportException) {
            $loggerData['error_code'] = $transportException->getCode();
            $loggerData['error_message'] = $transportException->getMessage();
            logger()->error($loggerPrefix . ' TransportException', $loggerData);
            report($transportException);
            try {
                DB::rollBack();
            } catch (\Throwable $throwable) {
                $loggerData['error_code'] = $throwable->getCode();
                $loggerData['error_message'] = $throwable->getMessage();
                logger()->critical($loggerPrefix . ' TransportException Rollback Throw', $loggerData);
                report($throwable);
            }
            $errorMessage = __('global.messages.mailSendError', ['title' => __(self::PROCESS_NAME)]);
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
            ->route('admin.admin.create')
            ->withInput()
            ->with('error', $errorMessage);

    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        $adminRoleGroups = AdminRoleGroup::all();
        $show = true;
        return Inertia::render(self::PAGE, compact('show', 'admin', 'adminRoleGroups'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        $adminRoleGroups = AdminRoleGroup::all();
        return Inertia::render(self::PAGE, compact('admin', 'adminRoleGroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRequest $adminRequest, Admin $admin)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        try {
            $validatedData = $adminRequest->validated();
            $loggerData = $validatedData;
            DB::beginTransaction();
            $admin->update($validatedData);
            DB::commit();
            return redirect()
                ->route('admin.admin.index')
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

        } catch (TransportException $transportException) {
            $loggerData['error_code'] = $transportException->getCode();
            $loggerData['error_message'] = $transportException->getMessage();
            logger()->error($loggerPrefix . ' TransportException', $loggerData);
            report($transportException);
            try {
                DB::rollBack();
            } catch (\Throwable $throwable) {
                $loggerData['error_code'] = $throwable->getCode();
                $loggerData['error_message'] = $throwable->getMessage();
                logger()->critical($loggerPrefix . ' TransportException Rollback Throw', $loggerData);
                report($throwable);
            }
            $errorMessage = __('global.messages.mailSendError', ['title' => __(self::PROCESS_NAME)]);
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
            ->route('admin.admin.edit', $admin)
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix, 'admin' => $admin->id]);
        try {
            $admin->delete();
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
}
