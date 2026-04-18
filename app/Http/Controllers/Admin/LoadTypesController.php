<?php


namespace App\Http\Controllers\Admin;


use App\Domain\LoadTypes\Filters\LoadTypeFilter;
use App\Domain\LoadTypes\Jobs\StoreLoadTypeJob;
use App\Domain\LoadTypes\Jobs\UpdateLoadTypeJob;
use App\Domain\LoadTypes\Models\LoadType;
use App\Domain\LoadTypes\Requests\LoadTypeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoadTypesController extends Controller
{
    public function index(Request $request)
    {
        $filter = new LoadTypeFilter($request);
        $loadTypes = LoadType::withTranslation()->filter($filter)->paginateFilter();

        return view('admin.load-types.index', [
            'loadTypes' => $loadTypes,
            'filters' => $filter->filters(),
        ]);
    }

    public function create()
    {
        return view('admin.load-types.create');
    }


    public function store(LoadTypeRequest $request)
    {
        try {
            $this->dispatchSync(new StoreLoadTypeJob($request));
            return redirect()->route('admin.load-types.index')->with('success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.load-types.index')->with('danger', trans('admin.store_failed'));
        }

    }

    public function edit(LoadType $loadType)
    {
        return view('admin.load-types.edit', [
            'loadType' => $loadType
        ]);
    }

    public function update(LoadType $loadType, LoadTypeRequest $request)
    {
        try {
            $this->dispatchSync(new UpdateLoadTypeJob($loadType, $request));
            return redirect()->route('admin.load-types.index')->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.load-types.index')->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(LoadType $loadType)
    {
        try {
            $loadType->delete();
            return redirect()->route('admin.load-types.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.load-types.index')->with('danger', trans('admin.destroy_failed'));
        }
    }
}
