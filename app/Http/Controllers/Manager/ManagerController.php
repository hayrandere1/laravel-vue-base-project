<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\ManagerRequest;
use App\Http\Resources\Manager\ManagerResource;
use App\Libraries\Helper;
use App\Models\Manager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Symfony\Component\Mailer\Exception\TransportException;

class ManagerController extends Controller
{
    const PREFIX = 'manager.manager';
    const PAGE = 'Manager/Manager/Form';
    const PROCESS_NAME = self::PREFIX . '.processName';

    public function __construct()
    {
        $this->authorizeResource(Manager::class);
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

        return Inertia::render('Manager/Manager/List', compact('resource'));
    }

    /**
     * @param array $filter
     * @return Builder
     */
    private function getListQuery(array $filter): HasMany
    {
        /* @var $query HasMany */
        $query = Auth::user()->company->managers()->orderBy($filter['orderColumn'], $filter['orderDirection']);
        $query->leftJoin('manager_role_groups', 'managers.role_group_id', '=', 'manager_role_groups.id');
        $query->selectRaw('managers.*, manager_role_groups.name as role_group');

        if (!empty($filter['search'])) {
            $query->where(function (Builder $query) use ($filter) {
                if (is_numeric($filter['search'])) {
                    $query->orWhere('managers.id', $filter['search']);
                }
                $query->orWhere('managers.username', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('managers.first_name', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('managers.last_name', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('managers.email', 'like', '%' . $filter['search'] . '%');
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

        $recordsTotal = Auth::user()->company->managers()->count();

        $datas = $query->paginate($filter['limit'])->appends($filter);

        if ($datas->count() === 0 && $request->get('page', 1) > 1) {
            return redirect()->route('manager.manager.index');
        }

        $resource = ManagerResource::collection($datas)->additional([
            'recordsTotal' => $recordsTotal,
            'filter' => $filter,
        ]);
        return $resource;
    }

    public function download(Request $request):JsonResponse
    {
        $this->authorize('download', Manager::class);

        $columns = [
            'id' => 'id',
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
            $fileName = 'manager_file_name';
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
        $managerRoleGroups = Auth::user()->company->managerRoleGroups;
        return Inertia::render(self::PAGE, compact('managerRoleGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ManagerRequest $managerRequest)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        try {
            $password = rand(100000, 999999);
            $dbPassword = Hash::make($password);
            $validatedData = $managerRequest->validated();
            $validatedData['password'] = $dbPassword;
            $loggerData = $validatedData;
            DB::beginTransaction();
            $manager = Auth::user()->company->managers()->create($validatedData);

            $manager->sendPasswordNotification($manager->username, $password);
            DB::commit();
            return redirect()
                ->route('manager.manager.index')
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
            ->route('manager.manager.create')
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Display the specified resource.
     */
    public function show(Manager $manager)
    {
        $managerRoleGroups = Auth::user()->company->managerRoleGroups;
        $show = true;
        return Inertia::render(self::PAGE, compact('show', 'manager', 'managerRoleGroups'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Manager $manager)
    {
        $managerRoleGroups = Auth::user()->company->managerRoleGroups;
        return Inertia::render(self::PAGE, compact('manager', 'managerRoleGroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ManagerRequest $managerRequest, Manager $manager)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        try {
            $validatedData = $managerRequest->validated();
            $loggerData = $validatedData;
            DB::beginTransaction();
            $manager->update($validatedData);
            DB::commit();
            return redirect()
                ->route('manager.manager.index')
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
            ->route('manager.manager.edit', $manager)
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manager $manager)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix, 'manager' => $manager->id]);
        try {
            $manager->delete();
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
