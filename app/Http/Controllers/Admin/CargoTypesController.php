<?php


namespace App\Http\Controllers\Admin;


use App\Domain\CargoTypes\Filters\CargoTypeFilter;
use App\Domain\CargoTypes\Jobs\StoreCargoTypeJob;
use App\Domain\CargoTypes\Jobs\UpdateCargoTypeJob;
use App\Domain\CargoTypes\Models\CargoType;
use App\Domain\CargoTypes\Requests\CargoTypesRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CargoTypesController extends Controller
{
    public function index(Request $request)
    {
        $filter = new CargoTypeFilter($request);
        $cargoTypes = CargoType::withTranslation()->filter($filter)->paginateFilter();

        return view('admin.cargo-types.index', [
            'cargoTypes' => $cargoTypes,
            'filters' => $filter->filters(),
        ]);
    }

    public function create()
    {
        return view('admin.cargo-types.create');
    }


    public function store(CargoTypesRequest $request)
    {
        try {
            $this->dispatchSync(new StoreCargoTypeJob($request));
            return redirect()->route('admin.cargo-types.index')->with('success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.cargo-types.index')->with('danger', trans('admin.store_failed'));
        }

    }

    public function edit(CargoType $cargoType)
    {
        return view('admin.cargo-types.edit', [
            'cargoType' => $cargoType
        ]);
    }

    public function update(CargoType $cargoType, CargoTypesRequest $request)
    {
        try {
            $this->dispatchSync(new UpdateCargoTypeJob($cargoType, $request));
            return redirect()->route('admin.cargo-types.index')->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.cargo-types.index')->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(CargoType $cargoType)
    {
        try {
            $cargoType->delete();
            return redirect()->route('admin.cargo-types.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.cargo-types.index')->with('danger', trans('admin.destroy_failed'));
        }
    }
}
