<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\PersonRequest;
use App\Http\Resources\User\PersonResource;
use App\Libraries\Helper;
use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Symfony\Component\Mailer\Exception\TransportException;

class PersonController extends Controller
{
    const PREFIX = 'user.person';
    const PAGE = 'User/Person/Form';
    const PROCESS_NAME = self::PREFIX . '.processName';

    public function __construct()
    {
        $this->authorizeResource(Person::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $sortableColumns = [
            'id',
            'group_id',
            'name',
            'email',
            'phone',
            'created_at',
            'updated_at'];
        $limits = [10, 25, 50, 100];

        $filter = [
            'search' => $request->get('search', ''),
            'group_id' => $request->get('group_id', ''),
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
                'title' => 'Group Name',
                'align' => 'start',
                'key' => 'group_name'
            ],
            [
                'title' => 'Name',
                'align' => 'start',
                'key' => 'name'
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

        return Inertia::render('User/Person/List', compact('resource'));
    }

    /**
     * @param array $filter
     * @return Builder
     */
    private function getListQuery(array $filter): HasManyThrough
    {
        /* @var $query HasManyThrough */
        $query = Auth::user()->company->people()->orderBy($filter['orderColumn'], $filter['orderDirection']);
        $query->selectRaw('people.*, groups.name as group_name');
        if (!empty($filter['search'])) {
            $query->where(function (Builder $query) use ($filter) {
                if (is_numeric($filter['search'])) {
                    $query->orWhere('people.id', $filter['search']);
                }
                $query->orWhere('people.name', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('people.email', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('people.phone', 'like', '%' . $filter['search'] . '%');
                return $query;
            });
        }
        return $query;
    }

    public function getData(Request $request)
    {
        $sortableColumns = [
            'id',
            'group_name',
            'name',
            'email',
            'phone',
            'created_at',
            'updated_at'];
        $limits = [10, 25, 50, 100];

        $filter = [
            'search' => $request->get('search', ''),
            'group_id' => $request->get('group_id', ''),
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

        $recordsTotal = Auth::user()->company->people()->count();

        $datas = $query->paginate($filter['limit'])->appends($filter);

        if ($datas->count() === 0 && $request->get('page', 1) > 1) {
            return redirect()->route('user.person.index');
        }

        $resource = PersonResource::collection($datas)->additional([
            'recordsTotal' => $recordsTotal,
            'filter' => $filter,
        ]);
        return $resource;
    }

    public function download(Request $request): JsonResponse
    {
        $this->authorize('download', Person::class);

        $columns = [
            'id' => 'id',
            'name' => 'name',
            'Group Name' => 'group_name',
            'Email' => 'email',
            'Phone' => 'phone',
            'created_at' => 'created_at'
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

            $fileName = 'group_file_name';
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
        $groups = Auth::user()->company->groups;
        return Inertia::render(self::PAGE, compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PersonRequest $personRequest)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        try {
            $loggerData = $personRequest->validated();
            DB::beginTransaction();
            Auth::user()->company->people()->create($personRequest->validated());
            DB::commit();
            return redirect()
                ->route('user.person.index')
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
            ->route('user.person.create')
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Display the specified resource.
     */
    public function show(Person $person)
    {
        $groups = Auth::user()->company->groups;
        $show = true;
        return Inertia::render(self::PAGE, compact('person', 'show', 'groups'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Person $person)
    {
        $groups = Auth::user()->company->groups;
        return Inertia::render(self::PAGE, compact('person', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PersonRequest $personRequest, Person $person)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix]);
        try {
            DB::beginTransaction();
            $person->update($personRequest->validated());
            DB::commit();
            return redirect()
                ->route('user.person.index')
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
            ->route('user.person.edit', $person)
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person)
    {
        $loggerPrefix = str_replace([__NAMESPACE__, '\\'], '', __METHOD__);
        Log::withContext(['function' => $loggerPrefix, 'person' => $person->id]);
        try {
            $person->delete();
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
