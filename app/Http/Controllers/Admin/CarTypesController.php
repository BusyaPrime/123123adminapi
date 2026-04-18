<?php


namespace App\Http\Controllers\Admin;


use App\Domain\CarTypes\Filters\CarFilter;
use App\Domain\CarTypes\Jobs\StoreCarTypeJob;
use App\Domain\CarTypes\Jobs\UpdateCarTypeJob;
use App\Domain\CarTypes\Jobs\UpdateCarTypeRestrictionsJob;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\CarTypes\Models\CarTypeRate;
use App\Domain\Regions\Models\Region;
use App\Domain\Seasons\Models\Season;
use App\Domain\Sizes\Models\Size;
use App\Domain\CarTypes\Requests\CarTypeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CarTypesController extends Controller
{
    public function index(Request $request)
    {
        $filter = new CarFilter($request);
        $carTypes = CarType::withTranslation()->filter($filter)->paginateFilter();

        return view('admin.car-types.index', [
            'carTypes' => $carTypes,
            'filters' => $filter->filters(),
        ]);
    }

    public function servingPrices(Request $r){
        $seasons = Season::all();
        $regions = Region::all();
        $currentSeasonId = $r->has('season_id') ? $r->input('season_id') : $seasons->first()->id;
        $currentRegionID = $r->has('region_from_id') ? $r->input('region_from_id') : $regions->first()->id;

        $carTypes = CarType::withTranslation()->with([
            'car_type_rates' => function($q) use($currentSeasonId, $currentRegionID){
                return $q->where('car_type_rates.season_id', $currentSeasonId)
                         ->where('region_from_id', $currentRegionID);
            }
        ])
        ->where('is_multi_region', 1)
        ->orderBy('priority', 'ASC')
        ->get();
        return view('admin.car-types.servingPricesTable',[
            'seasons' => $seasons,
            'currentSeasonId' => $currentSeasonId,
            'currentRegionID' => $currentRegionID,
            'regions' => $regions,
            'carTypes' => $carTypes,
            'request' => $r
        ]);
    }

    public function updateServingPrices(Request $r){
        try {

            $season = Season::first();
            $pricesFull = $r->input('prices_full', []);
            $pricesPartial = $r->input('prices_partial', []);
            $region_from_id = $r->input('region_from_id');
            $region_to_ids = $r->input('region_to_ids');
            $car_type_ids = $r->input('car_type_ids', []);
            $season_id = $r->input('season_id', $season->id);
            $isPartial = ($r->segment(2) != '' && $r->segment(2) == 'update_serving_prices_partial') ? true : false;

            $changedFields = [];

            for($i = 0; $i < count($pricesFull); $i++){
                $priceFull = (int) str_replace(' ', '', $pricesFull[$i]);
                $pricePartial = (int) str_replace(' ', '', $pricesPartial[$i]);
                $region_to_id = $region_to_ids[$i]['region_to_id'];
                $car_type_id = $car_type_ids[$i]['car_type_id'];

                $carType = CarType::find($car_type_id);
                
                if((($priceFull > 0 && $priceFull != '') || ($pricePartial > 0 && $pricePartial != ''))){
                        $rate = CarTypeRate::where([
                            'region_from_id' => $region_from_id,
                            'region_to_id' => $region_to_id,
                            'car_type_id' => $car_type_id,
                            'season_id' => $season_id,
                        ])->first();

                    if($rate){
                        $rate->not_full_min_price = $priceFull;
                        $rate->min_price = $pricePartial;
                        $rate->save();
                        array_push($changedFields, [$region_from_id.'_'.$region_to_id.'_'.$car_type_id => $priceFull]);
                    }
                }
            }
            $queryParams = [];

            if($r->has('region_from_id')) $queryParams['region_from_id'] = $r->input('region_from_id');
            if($r->has('season_id')) $queryParams['season_id'] = $r->input('season_id');
            $queryParams['changed_fields'] = $changedFields;

            return redirect()->route('admin.car-types.servingPrices', $queryParams)->withInput()->with('success', trans('admin.store_success'));
        } catch (\Throwable $th) {
            report($th);
            return redirect()->route('admin.car-types.servingPrices')->withInput()->with('danger', trans('admin.update_failed'));
        }
    }

    public function create()
    {
        $sizes = Size::orderBy('priority')->get();
        return view('admin.car-types.create', [
            'sizes' => $sizes
        ]);
    }


    public function store(CarTypeRequest $request)
    {
        try {
            $this->dispatchSync(new StoreCarTypeJob($request));
            return redirect()->route('admin.car-types.index')->with('success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.car-types.index')->with('danger', trans('admin.store_failed'));
        }

    }

    public function edit(CarType $carType)
    {
        $sizes = Size::orderBy('priority')->get();
        return view('admin.car-types.edit', [
            'carType' => $carType,
            'sizes' => $sizes
        ]);
    }
    
    public function showRestrictions(CarType $carType)
    {
        $regions = Region::select('id', 'title')->get();
        return view('admin.car-types.restrictions', [
            'carType' => $carType,
            'regions' => $regions
        ]);
    }

    public function update(CarType $carType, CarTypeRequest $request)
    {
        try {
            $this->dispatchSync(new UpdateCarTypeJob($carType, $request));
            return redirect()->route('admin.car-types.edit', $carType)->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.car-types.edit', $carType)->with('danger', trans('admin.update_failed'));
        }
    }
    
    public function updateRestriction(CarType $carType, Request $request)
    {
        try {
            $this->dispatchSync(new UpdateCarTypeRestrictionsJob($carType, $request));
            return redirect()->route('admin.car-types.restrictions', $carType)->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.car-types.restrictions', $carType)->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(CarType $carType)
    {
        try {
            $carType->deleteImage();
            $carType->delete();
            return redirect()->route('admin.car-types.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.car-types.index')->with('danger', trans('admin.destroy_failed'));
        }
    }
}
