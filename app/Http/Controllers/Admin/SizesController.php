<?php


namespace App\Http\Controllers\Admin;


use App\Domain\Sizes\Filters\SizeFilter;
use App\Domain\Sizes\Jobs\StoreSizeJob;
use App\Domain\Sizes\Jobs\UpdateSizeJob;
use App\Domain\Sizes\Models\Size;
use App\Domain\Sizes\Requests\SizeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SizesController extends Controller
{
    public function index(Request $request)
    {
        $filter = new SizeFilter($request);
        $sizes = Size::withTranslation()->filter($filter)->paginateFilter();

        return view('admin.sizes.index', [
            'sizes' => $sizes,
            'filters' => $filter->filters(),
        ]);
    }

    public function create()
    {
        return view('admin.sizes.create');
    }


    public function store(SizeRequest $request)
    {
        try {
            $this->dispatchSync(new StoreSizeJob($request));
            return redirect()->route('admin.sizes.index')->with('success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.sizes.index')->with('danger', trans('admin.store_failed'));
        }

    }

    public function edit(Size $size)
    {
        return view('admin.sizes.edit', [
            'size' => $size
        ]);
    }

    public function update(Size $size, SizeRequest $request)
    {
        try {
            $this->dispatchSync(new UpdateSizeJob($size, $request));
            return redirect()->route('admin.sizes.index')->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.sizes.index')->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(Size $size)
    {
        try {
            $size->deleteImage();
            $size->delete();
            return redirect()->route('admin.sizes.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.sizes.index')->with('danger', trans('admin.destroy_failed'));
        }
    }
}
