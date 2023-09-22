<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    const PREFIX = 'admin.user';
    const PAGE = 'Admin/User/Form';
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
            'company_name',
            'username',
            'first_name',
            'last_name',
            'email',
            'phone',
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
            'filter' => $filter,
        ];

        return Inertia::render('Admin/User/List', compact('resource'));
    }
    /**
     * @param array $filter
     * @return Builder
     */
    private function getListQuery(array $filter): Builder
    {
        /* @var $query Builder */
        $query = User::orderBy($filter['orderColumn'], $filter['orderDirection']);
        $query->leftJoin('companies', 'companies.id', '=', 'users.id');
        $query->selectRaw('users.*, companies.name as company_name');
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


    public function getData(Request $request)
    {
        $sortableColumns = [
            'id',
            'company_name',
            'username',
            'first_name',
            'last_name',
            'email',
            'phone',
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

        $recordsTotal = User::count();

        $datas = $query->paginate($filter['limit'])->appends($filter);

        if ($datas->count() === 0 && $request->get('page', 1) > 1) {
            return redirect()->route('admin.admin.index');
        }

        $resource = UserResource::collection($datas)->additional([
            'recordsTotal' => $recordsTotal,
            'filter' => $filter,
        ]);
        return $resource;
    }
}
