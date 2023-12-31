<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyRequest;
use App\Http\Resources\Admin\CompanyResource;
use App\Libraries\Helper;
use App\Models\Company;
use App\Models\Package;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Symfony\Component\Mailer\Exception\TransportException;

class CompanyController extends Controller
{

    const PREFIX = 'admin.company';
    const PAGE = 'Admin/Company/Form';
    const PROCESS_NAME = self::PREFIX . '.processName';

    public function __construct()
    {
        $this->authorizeResource(Company::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortableColumns = [
            'id',
            'name',
            'user_count',
            'manager_count',
            'is_active',
            'due_date',
            'deleted_at',
            'created_at',
            'updated_at'];
        $limits = [10, 25, 50, 100];

        $filter = [
            'packageId' => $request->get('packageId', ''),
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
                'title' => 'Manager Count',
                'align' => 'start',
                'key' => 'manager_count'
            ],
            [
                'title' => 'Active',
                'align' => 'start',
                'key' => 'is_active'
            ],
            [
                'title' => 'Due Date',
                'align' => 'start',
                'key' => 'due_date'
            ],
            [
                'title' => 'Delete at',
                'align' => 'start',
                'key' => 'deleted_at'
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
        $userColumns = [
            [
                'title' => 'ID',
                'align' => 'start',
                'key' => 'id'
            ],
            [
                'title' => 'Company Name',
                'align' => 'start',
                'key' => 'company_name'
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
                'title' => 'Phone',
                'align' => 'start',
                'key' => 'phone'
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
        $managerColumns = [
            [
                'title' => 'ID',
                'align' => 'start',
                'key' => 'id'
            ],
            [
                'title' => 'Company Name',
                'align' => 'start',
                'key' => 'company_name'
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
                'title' => 'Phone',
                'align' => 'start',
                'key' => 'phone'
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
            'userColumns' => $userColumns,
            'managerColumns' => $managerColumns,
            'filter' => $filter,
        ];

        return Inertia::render('Admin/Company/List', compact('resource'));
    }

    /**
     * @param array $filter
     * @return Builder
     */
    private function getListQuery(array $filter): Builder
    {
        /* @var $query Builder */
        $query = Company::orderBy($filter['orderColumn'], $filter['orderDirection']);
        $query->leftJoin('users', 'users.company_id', '=', 'companies.id');
        $query->leftJoin('managers', 'managers.company_id', '=', 'companies.id');
        $query->selectRaw('companies.*, count(users.id) as user_count, count(managers.id) as manager_count');
        $query->groupBy('companies.id');
        if (!empty($filter['search'])) {
            $query->where(function (Builder $query) use ($filter) {
                if (is_numeric($filter['search'])) {
                    $query->orWhere('companies.id', $filter['search']);
                }
                $query->orWhere('companies.name', 'like', '%' . $filter['search'] . '%');
                return $query;
            });
        }
        if (!empty($filter['packageId'])) {
            $query->where('companies.package_id', $filter['packageId']);
        }
        return $query;
    }


    public function getData(Request $request)
    {
        $sortableColumns = [
            'id',
            'name',
            'user_count',
            'manager_count',
            'is_active',
            'due_date',
            'deleted_at',
            'created_at',
            'updated_at'];
        $limits = [10, 25, 50, 100];

        $filter = [
            'packageId' => $request->get('packageId', ''),
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

        $recordsTotal = Company::count();

        $datas = $query->paginate($filter['limit'])->appends($filter);

        if ($datas->count() === 0 && $request->get('page', 1) > 1) {
            return redirect()->route('admin.company.index');
        }

        $resource = CompanyResource::collection($datas)->additional([
            'recordsTotal' => $recordsTotal,
            'filter' => $filter,
        ]);
        return $resource;
    }


    public function download(Request $request): JsonResponse
    {
        $this->authorize('download', Company::class);

        $columns = [
            'ID' => 'id',
            'Name' => 'name',
            'User Count' => 'user_count',
            'Manager Count' => 'manager_count',
            'Is Active' => 'is_active',
            'Due Date' => 'due_date',
            'Deleted At' => 'deleted_at',
            'Created At' => 'created_at'
        ];

        $filter = [
            'packageId' => $request->get('packageId', ''),
            'search' => $request->get('search', ''),
            'orderColumn' => 'id',
            'orderDirection' => 'desc'
        ];

        $query = $this->getListQuery($filter);
        $query2 = $this->getListQuery($filter);
        $datas = $query2->paginate(1);
        $filteredCount = $datas->total();
        if ($filteredCount > 0) {

            $fileName = 'company_file_name';
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
        $packages = Package::where('is_active', true)->get()->toArray();
        return Inertia::render(self::PAGE, compact('packages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $companyRequest)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        try {
            $validatedData = $companyRequest->validated();
            $loggerData = $validatedData;
            DB::beginTransaction();
            $package = Package::find($companyRequest->package_id);
            $company = Company::create($validatedData);
            $passwordManager = rand(100000, 999999);
            $validatedData['supervisor']['password'] = Hash::make($passwordManager);
            $validatedData['supervisor']['is_active'] = 1;
            $validatedData['supervisor']['role_group_id'] = $package->managerRoleGroup->id;
            $passwordUser = rand(100000, 999999);
            $validatedData['mainUser']['password'] = Hash::make($passwordUser);
            $validatedData['mainUser']['is_active'] = 1;
            $validatedData['mainUser']['role_group_id'] = $package->userRoleGroup->id;
            $manager = $company->managers()->create($validatedData['supervisor']);
            $user = $company->users()->create($validatedData['mainUser']);
            $company->update([
                'supervisor_id' => $manager->id,
                'main_user_id' => $user->id
            ]);
            $manager->sendPasswordNotification($manager->username, $passwordManager);
            $user->sendPasswordNotification($user->username, $passwordUser);
            DB::commit();
            return redirect()
                ->route('admin.company.index')
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
            ->route('admin.company.create')
            ->withInput()
            ->with('error', $errorMessage);

    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $show = true;
        $packages = Package::where('is_active', true)->get()->toArray();
        $company->setAttribute('supervisor', $company->supervisor());
        $company->setAttribute('mainUser', $company->mainUser());
        return Inertia::render(self::PAGE, compact('show', 'packages', 'company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        $packages = Package::where('is_active', true)->get()->toArray();
        $company->setAttribute('supervisor', $company->supervisor());
        $company->setAttribute('mainUser', $company->mainUser());
        return Inertia::render(self::PAGE, compact('company', 'packages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $companyRequest, Company $company)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        try {
            $validatedData = $companyRequest->validated();
            $loggerData = $validatedData;
            DB::beginTransaction();
            $company->update($validatedData);
            $package = Package::find($companyRequest->package_id);
            $validatedData['supervisor']['role_group_id'] = $package->managerRoleGroup->id;
            $validatedData['mainUser']['role_group_id'] = $package->userRoleGroup->id;
            $company->supervisor()->update($validatedData['supervisor']);
            $company->mainUser()->update($validatedData['mainUser']);
            DB::commit();
            return redirect()
                ->route('admin.company.index')
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
            ->route('admin.company.edit', $company)
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix, 'company' => $company->id]);
        try {
            $company->delete();
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

    public function mainUserLogin(Company $company)
    {
//        $this->authorize('login', $company);
        return redirect('/Admin/UserScreen/' . $company->mainUser()->id);
    }

    public function supervisorUserLogin(Company $company)
    {
//        $this->authorize('login', $company);
        return redirect('/Admin/ManagerScreen/' . $company->supervisor()->id);
    }
}
