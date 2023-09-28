<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\Manager\ArchiveResource;
use App\Models\Archive;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ArchiveController extends Controller
{
    const PREFIX = 'manager.archive';

    public function __construct()
    {
        $this->authorizeResource(Archive::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortableColumns = [
            'id',
            'file_name',
            'status',
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
                'title' => 'File Name',
                'align' => 'start',
                'key' => 'file_name'
            ],
            [
                'title' => 'Status',
                'align' => 'start',
                'key' => 'status'
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

        return Inertia::render('Manager/Archive/List', compact('resource'));
    }

    /**
     * @param array $filter
     * @return Builder
     */
    private function getListQuery(array $filter): HasMany
    {
        /* @var $query HasMany */
        $query = Auth::user()->archives()->orderBy($filter['orderColumn'], $filter['orderDirection']);
        if (!empty($filter['search'])) {
            $query->where(function (Builder $query) use ($filter) {
                if (is_numeric($filter['search'])) {
                    $query->orWhere('archives.id', $filter['search']);
                }
                $query->orWhere('archives.file_name', 'like', '%' . $filter['search'] . '%');
                $query->orWhere('archives.status', 'like', '%' . $filter['search'] . '%');
                return $query;
            });
        }
        return $query;
    }

    public function getData(Request $request)
    {
        $sortableColumns = [
            'id',
            'file_name',
            'status',
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

        $recordsTotal = Auth::user()->archives()->count();

        $datas = $query->paginate($filter['limit'])->appends($filter);

        if ($datas->count() === 0 && $request->get('page', 1) > 1) {
            return redirect()->route('manager.archives.index');
        }

        $resource = ArchiveResource::collection($datas)->additional([
            'recordsTotal' => $recordsTotal,
            'filter' => $filter,
        ]);
        return $resource;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Archive $archive)
    {
//        Storage::delete(storage_path('app/public/list_csv/' . $archive->unique_id . '.csv'))
    }

    public function download(Archive $archive)
    {
        $this->authorize('download', $archive);
        return response()->download(storage_path('app/public/list_csv/' . $archive->unique_id . '.csv'), $archive->file_name . '.csv');
    }
}
