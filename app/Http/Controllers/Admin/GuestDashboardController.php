<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GuestDashboardRequest;
use App\Http\Resources\Admin\GuestDashboardResource;
use App\Libraries\Helper;
use App\Models\Company;
use App\Models\GuestDashboard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Symfony\Component\Mailer\Exception\TransportException;

class GuestDashboardController extends Controller
{
    const PREFIX = 'admin.guest_dashboard';
    const PAGE = 'Admin/GuestDashboard/Form';
    const PROCESS_NAME = self::PREFIX . '.processName';

    public function __construct()
    {
        $this->authorizeResource(GuestDashboard::Class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortableColumns = [
            'id',
            'company_name',
            'name',
            'url',
            'is_active',
            'created_at',
            'updated_at',
        ];
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
                'title' => 'Company Name',
                'align' => 'start',
                'key' => 'company_name'
            ],
            [
                'title' => 'Name',
                'align' => 'start',
                'key' => 'name'
            ],
            [
                'title' => 'Url',
                'align' => 'start',
                'key' => 'url'
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

        return Inertia::render('Admin/GuestDashboard/List', compact('resource'));
    }


    /**
     * @param array $filter
     * @return Builder
     */
    private function getListQuery(array $filter): Builder
    {
        /* @var $query Builder */
        $query = GuestDashboard::whereNull('company_id')->orderBy($filter['orderColumn'], $filter['orderDirection']);

        if (!empty($filter['search'])) {
            $query->where(function (Builder $query) use ($filter) {
                if (is_numeric($filter['search'])) {
                    $query->orWhere('id', $filter['search']);
                }
                $query->orWhere('name', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('url', 'like', '%' . $filter['search'] . '%');
                return $query;
            });
        }
        return $query;
    }

    public function getData(Request $request)
    {
        $sortableColumns = [
            'id',
            'company_name',
            'name',
            'url',
            'is_active',
            'created_at',
            'updated_at'
        ];
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

        $recordsTotal = GuestDashboard::whereNull('company_id')->count();

        $datas = $query->paginate($filter['limit'])->appends($filter);

        if ($datas->count() === 0 && $request->get('page', 1) > 1) {
            return redirect()->route('admin.admin.index');
        }

        $resource = GuestDashboardResource::collection($datas)->additional([
            'recordsTotal' => $recordsTotal,
            'filter' => $filter,
        ]);
        return $resource;
    }


    public function download(Request $request): JsonResponse
    {
        $this->authorize('download', GuestDashboard::class);

        $columns = [
            'ID' => 'id',
            'Company Name' => 'company_name',
            'Name' => 'name',
            'Url' => 'url',
            'Content' => 'content',
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

            $fileName = 'guest_dashboard_file_name';
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
        $companies = Company::all();
        return Inertia::render(self::PAGE, compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GuestDashboardRequest $guestDashboardRequest)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        try {
            $validatedData = $guestDashboardRequest->validated();
            $validatedData['content'] = [];
            foreach ($guestDashboardRequest->validated()['content'] as $value) {
                $value['content'] = '';
                $validatedData['content'][] = $value;
            }
            $validatedData['content']=json_encode($validatedData['content']);
            $loggerData = $validatedData;
            DB::beginTransaction();
            GuestDashboard::create($validatedData);
            DB::commit();
            return redirect()
                ->route('admin.guest_dashboard.index')
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
            ->route('admin.guest_dashboard.create')
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Display the specified resource.
     */
    public function show(GuestDashboard $guestDashboard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GuestDashboard $guestDashboard)
    {
        $companies = Company::all();
        $guestDashboard->content=json_decode($guestDashboard->content);
        return Inertia::render(self::PAGE, compact('guestDashboard', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GuestDashboardRequest $guestDashboardRequest, GuestDashboard $guestDashboard)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        try {
            $validatedData = $guestDashboardRequest->validated();
            $validatedData['content'] = [];
            foreach ($guestDashboardRequest->validated()['content'] as $value) {
                $value['content'] = '';
                $validatedData['content'][] = $value;
            }
            $validatedData['content']=json_encode($validatedData['content']);
            DB::beginTransaction();
            $guestDashboard->update($validatedData);
            DB::commit();
            return redirect()
                ->route('admin.guest_dashboard.index')
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
            ->route('admin.guest_dashboard.edit', $guestDashboard)
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GuestDashboard $guestDashboard)
    {
        //
    }
}
