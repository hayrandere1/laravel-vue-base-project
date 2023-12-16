<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    const PREFIX = 'user.user';
    const PAGE = 'User/User/Form';
    const PROCESS_NAME = self::PREFIX . '.processName';

    public function __construct()
    {
        $this->authorizeResource(User::class);
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

        $recordsTotal = Auth::user()->company->users()->count();

        $datas = $query->paginate($filter['limit'])->appends($filter);

        if ($datas->count() === 0 && $request->get('page', 1) > 1) {
            return redirect()->route('user.user.index');
        }

        $resource = UserResource::collection($datas)->additional([
            'recordsTotal' => $recordsTotal,
            'filter' => $filter,
        ]);
        return $resource;
    }

    /**
     * @param array $filter
     * @return Builder
     */
    private function getListQuery(array $filter): HasMany
    {
        /* @var $query HasMany */
        $query = Auth::user()->company->users()->orderBy($filter['orderColumn'], $filter['orderDirection']);
        $query->leftJoin('user_role_groups', 'users.role_group_id', '=', 'user_role_groups.id');
        $query->selectRaw('users.*, user_role_groups.name as role_group');

        if (!empty($filter['search'])) {
            $query->where(function (Builder $query) use ($filter) {
                if (is_numeric($filter['search'])) {
                    $query->orWhere('users.id', $filter['search']);
                }
                $query->orWhere('users.username', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('users.first_name', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('users.last_name', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('users.email', 'like', '%' . $filter['search'] . '%');
                return $query;
            });
        }
        return $query;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userRoleGroups = Auth::user()->company->userRoleGroups()->get([DB::raw('`name` as `label`'),DB::raw('`id` as `value`')])->toArray();
        return new JsonResponse([
            'userRoleGroups' => $userRoleGroups
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
