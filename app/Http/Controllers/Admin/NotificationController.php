<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\NotificationResource;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    const PREFIX = 'admin.notification';
    const PROCESS_NAME = self::PREFIX . '.processName';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $sortableColumns = [
            'id',
            'title',
            'content',
            'is_read',
            'created_at'];
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
                'title' => 'Title',
                'align' => 'start',
                'key' => 'title'
            ],
            [
                'title' => 'Content',
                'align' => 'start',
                'key' => 'content'
            ],
            [
                'title' => 'Is Read',
                'align' => 'start',
                'key' => 'is_read'
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

        return Inertia::render('Admin/Notification/List', compact('resource'));
    }

    /**
     * @param array $filter
     * @return Builder
     */
    private function getListQuery(array $filter): HasMany
    {
        /* @var $query HasMany */
        $query = Auth::user()->notifications()->orderBy($filter['orderColumn'], $filter['orderDirection']);
        if (!empty($filter['search'])) {
            $query->where(function (Builder $query) use ($filter) {
                if (is_numeric($filter['search'])) {
                    $query->orWhere('notifications.id', $filter['search']);
                }
                $query->orWhere('notifications.title', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('notifications.content', 'like', '%' . $filter['search'] . '%');
                return $query;
            });
        }
        return $query;
    }

    public function getData(Request $request)
    {
        $sortableColumns = [
            'id',
            'title',
            'content',
            'is_read',
            'created_at'];
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

        $recordsTotal = Auth::user()->notifications()->count();

        $datas = $query->paginate($filter['limit'])->appends($filter);

        if ($datas->count() === 0 && $request->get('page', 1) > 1) {
            return redirect()->route('user.notification.index');
        }

        $resource = NotificationResource::collection($datas)->additional([
            'recordsTotal' => $recordsTotal,
            'filter' => $filter,
        ]);
        return $resource;
    }

    public function getNotifications(): JsonResponse
    {
        return new JsonResponse([
            'unread_count' => Auth::user()->notifications()->where('is_read', 0)->count(),
            'notifications' => Auth::user()->notifications()->orderBy('id', 'DESC')->limit(3)->get()
        ]);
    }

    public function markAllRead(): JsonResponse
    {
        try {
            Auth::user()->notifications()->update(['is_read' => true]);
            return new JsonResponse([
                'process' => true,
                'unread_count' => Auth::user()->notifications()->where('is_read', 0)->count(),
                'notifications' => Auth::user()->notifications()->orderBy('id', 'DESC')->limit(3)->get()
            ]);
        } catch (\Throwable $throwable) {
            return new JsonResponse([
                'process' => false,
                'title' => 'Update Unknown Error'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(Notification $notification)
    {
        $notification->update(['is_read' => true]);
        if ($notification->link) {
            return redirect($notification->link);
        } else {
            return redirect()->back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        //
    }
}
