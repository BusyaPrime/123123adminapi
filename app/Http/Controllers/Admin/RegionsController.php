<?php


namespace App\Http\Controllers\Admin;


use App\Domain\Regions\Models\Region;
use App\Domain\Regions\Filters\RegionsFilter;
use App\Domain\Seasons\Models\Season;
use App\Domain\Regions\Exports\TariffsExport;
use App\Domain\Regions\Requests\TariffsRequest;
use App\Domain\Regions\Resources\TariffsCollectionResource;
use App\Domain\CarTypes\Models\CarTypeRate;
use App\Domain\CarTypes\Models\CarTypeRatesHistory;
use App\Domain\CarTypes\Jobs\CarTypeRatesHistoryJob;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\Users\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Regions\Jobs\ChangeDistancesJob;

class RegionsController extends Controller
{
    public function index()
    {
        $regions = Region::paginate();
        return view('admin.regions.index', ['regions' => $regions]);
    }

    public function changeByPriceView(Request $r){
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
        // ->orderBy('max_weight', 'ASC')
        ->orderBy('priority', 'ASC')
        ->get();
        return view('admin.tariffs.changeByPrice',[
            'seasons' => $seasons,
            'currentSeasonId' => $currentSeasonId,
            'currentRegionID' => $currentRegionID,
            'regions' => $regions,
            'carTypes' => $carTypes,
            'request' => $r
        ]);
    }

    public function updateTariffByPrice(Request $r){
        try {
            $season = Season::first();
            $prices = $r->input('prices', []);
            $region_from_id = $r->input('region_from_id');
            $region_to_ids = $r->input('region_to_ids');
            $car_type_ids = $r->input('car_type_ids', []);
            $season_id = $r->input('season_id', $season->id);

            $changedFields = [];

            for($i = 0; $i < count($prices); $i++){
                $price = (int) str_replace(' ', '', $prices[$i]);
                $region_to_id = $region_to_ids[$i]['region_to_id'];
                $car_type_id = $car_type_ids[$i]['car_type_id'];

                $carType = CarType::find($car_type_id);
                
                $rate = CarTypeRate::where([
                    'region_from_id' => $region_from_id,
                    'region_to_id' => $region_to_id,
                    'car_type_id' => $car_type_id,
                    'season_id' => $season_id,
                ])->first();

                if($rate && ($price != 0 && $price != '') && $rate->distance_between > 0){
                    $rateDistances = json_decode($rate->prices_distance, true);
                    $editIndex = 0;
                    $weight = $carType->max_weight;
                    $distance = $rate->distance_between;
                    $minPrice = $rate->min_price;

                    foreach($rateDistances as $index => $rateDistance){
                        if($distance <= $rateDistance['distance']){
                            $editIndex = $index;
                            break;
                        }
                        $editIndex = $index;
                    }

                    $rateRation = ($price - $rate->min_price) / ($distance * $carType->max_weight);
                    
                    $newRate = ($price - $minPrice) / ($distance * $weight);
                    

                    
                    // $newRate = (double) substr($newRate, 0, 4);
                    $rateDistances[$editIndex]['sum'] = $newRate;

                    $rate->prices_distance = json_encode($rateDistances);
                    $rate->save();
                    array_push($changedFields, [$region_from_id.'_'.$region_to_id.'_'.$car_type_id => $price]);
                }
            }
            $queryParams = [];

            if($r->has('region_from_id')) $queryParams['region_from_id'] = $r->input('region_from_id');
            if($r->has('season_id')) $queryParams['season_id'] = $r->input('season_id');
            $queryParams['changed_fields'] = $changedFields;

            return redirect()->route('admin.regions.changeByPrice', $queryParams)->withInput()->with('success', trans('admin.store_success'));
        } catch (\Throwable $th) {
            return redirect()->route('admin.regions.changeByPrice')->withInput()->with('danger', trans('admin.update_failed'));
        }
    }

    public function tracking(Request $request)
    {
        $hasOrders = $request->input('has_order', 'all');
        $phoneNumber = $request->input('driver_phone');
        $carNumber = $request->input('car_number');
        $carTypeId = $request->input('car_type_id');

        $filters = [
            'has_order' => $hasOrders,
            'driver_phone' => $phoneNumber,
            'car_number' => $carNumber,
            'car_type_id' => $carTypeId,
        ];

        $query = User::whereHas('car')->with('profile');

        if ($phoneNumber) {
            $query->where('username', 'like', '%'.$phoneNumber.'%');
        }

        if ($carNumber) {
            $query->whereHas('car', function ($q) use ($carNumber) {
                $q->where('number', 'like', '%'.$carNumber.'%');
            });
        }

        if ($carTypeId) {
            $query->whereHas('car', function ($q) use ($carTypeId) {
                $q->where('car_type_id',  $carTypeId);
            });
        }

        $center = [41.326681, 69.244031];
        $onlineUsers = $query->get();
        foreach ($onlineUsers as $user) {
            if ($user->profile && $user->profile->lat && $user->profile->lng) {
                $center = [$user->profile->lat, $user->profile->lng];
                break;
            }
        }
        
        return view('admin.regions.tracking', [
            'onlineUsers' => $onlineUsers,
            'filters' => $filters,
            'center' => $center,
        ]);
    }

    public function edit(Region $region){
        return view('admin.regions.edit', ['region' => $region]);
    }

    public function update(Region $region, Request $request)
    {
        try {
            


        } catch (\Throwable $err) {
            
        }
    }
    
    
    public function manageTariffs(Request $request)
    {
        $regions = Region::select('id', 'title')->get();
        $seasons = Season::get();
        $currentSeasonId = request()->get('season_id') ?? $seasons[0]->id;
        $carTypes = CarType::get();

        $regions_from = Region::select('id', 'title')->where(function ($q) use($request){
            if($request->filled('region_from')){
                $q->where('id', $request->input('region_from'));
            }
        })->get();
        $regions_to = Region::select('id', 'title')->where(function ($q) use($request){
            if($request->filled('region_to')){
                $q->where('id', $request->input('region_to'));
            }
        })->get();

        $directions = collect([]);

        for ($i = 0; $i < count($regions_from); $i++) {
            for($j = 0; $j < count($regions_to); $j++){
                $directions->push([
                    'region_from_id' => $regions_from[$i]->id,
                    'region_to_id' => $regions_to[$j]->id,
                    'region_from' => $regions_from[$i]->title,
                    'region_to' => $regions_to[$j]->title,
                ]);
            }
        }

        $itemsPerPage = 15;
        $page = $_GET['page'] ?? 1;

        $paginationsCount = (int)ceil(count($directions)/$itemsPerPage);
        $directions = $directions->forPage($page, $itemsPerPage);

        return view(
            'admin.tariffs.manage',
            [
                'directions' => $directions,
                'carTypes' => $carTypes,
                'seasons' => $seasons,
                'regions' => $regions,
                'currentSeasonId' => $currentSeasonId,
                'paginationsCount' => $paginationsCount,
                'page' => $page,
            ]
        );
    }

    private function cyrToLatReverse($str, $lang = 'lat') {
        $cyr = [
            'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п', 'к',
            'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
            'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
            'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
        ];
        $lat = [
            'a','b','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p', 'q',
            'r','s','t','u','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya',
            'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P',
            'R','S','T','U','F','H','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya'
        ];
        return $lang == 'lat' ? str_replace($lat, $cyr, $str) : str_replace($cyr, $lat, $str);
    }

    public function filterTariffs(Request $request){
        $season_id = $request->input('season_id');
        $search = trim((string) $request->input('search', ''));

        if ($search === '') {
            return TariffsCollectionResource::collection(collect([]));
        }

        $qStr = explode('-', $search);

        $from = htmlspecialchars($this->cyrToLatReverse($qStr[0]));
        $to = isset($qStr[1]) ? $this->cyrToLatReverse($qStr[1]) : '';

        $region_from = Region::select('id', 'title')->where('title', 'like', '%'.$from.'%')->get();
        $region_to = Region::select('id', 'title')->where('title', 'like', '%'.$to.'%')->get();
        $carTypes = CarType::withTranslation()->orderBy('priority')->get();
        $directions = collect([]);

        foreach ($region_from as $region_f) {
            foreach ($region_to as $j => $region_t) {
                $cars = collect();
                foreach ($carTypes as  $car) {
                    $carTypeRate = CarTypeRate::where('region_from_id', $region_f->id)->where('region_to_id', $region_t->id)->where('car_type_id', $car->id)->where('season_id', $season_id)->first();
                    $cars->push([
                        'id' => $car->id,
                        'title' => $car->title,
                        'icon' => $car->imageUrl(),
                        'common_coefficient' => $carTypeRate->common_coefficient ?? 0,
                    ]);
                }
                $directions->add([
                    'region_from_id' => $region_f->id,
                    'region_to_id' => $region_t->id,
                    'region_from' => $region_f->title,
                    'region_to' => $region_t->title,
                    'carType' => $cars
                ]);
            }
        }

        return TariffsCollectionResource::collection($directions);

    }


    public function manageTariffsUpdate(Request $request){
        try {
            $region_from = (int)$request->input('region_from_id', 0);
            $region_to = (int)$request->input('region_to_id', 0);
            $coeff = $request->input('common_coeff', 0);
            $checkedCars = $request->input('checkedCars', []);
            $activeSeasonId = $request->input('season_id', Season::get()->first()->id);

            if(!$region_from || !$region_to || !$activeSeasonId) {
                abort(404);
            }
            
            if(count($checkedCars) != 0){
                for($i = 0; $i < count($checkedCars); $i++){
                    $rate = CarTypeRate::where('region_from_id', $region_from)
                                    ->where('region_to_id', $region_to)
                                    ->where('car_type_id', $checkedCars[$i])
                                    ->where('season_id', $activeSeasonId)->first();
                        if($rate){
                            $this->dispatchSync(new CarTypeRatesHistoryJob($rate, $request));
                            $rate->common_coefficient = $coeff;
                            $rate->save();
                        }
                }
                return redirect()->route('admin.regions.manageTariffs', [ 'region_from' => $region_from, 'currentSeasonId' => $activeSeasonId, 'changed' => "ch_" . $region_from . "_" . $region_to])->with('success', trans('admin.update_success'));
            } else{
                return redirect()->route('admin.regions.manageTariffs', ['region_from' => $region_from, 'currentSeasonId' => $activeSeasonId, 'changed' => "ch_".$region_from."_".$region_to])->with('danger', trans('admin.update_failed'));
            }
        } catch (\Exception $err) {
            return redirect()->route('admin.regions.manageTariffs')->with('danger', trans('admin.update_failed'));
        }
    }

    public function distances(Request $request){
        $regions = Region::get();
        $seasons = Season::all();
        $season_id = $request->input('season_id', $seasons->first()->id);
        $carType = CarType::first();
        
        $distances = collect([]);
        $regionsCollection = collect([]);

        foreach($regions as $regionFrom){
            $regionsCollection->add($regionFrom);
            $r = collect([]);

            for($i = 0; $i <= $regions->count(); $i++){
                if($i == 0){
                    $r->add($regionFrom);
                } else {
                    $rate = CarTypeRate::where('region_from_id', $regionFrom->id)
                                    ->where('region_to_id', $regions[$i - 1]->id)
                                    ->where('car_type_id', $carType->id)
                                    ->where('season_id', $season_id)
                                    ->first();
                    if($rate){
                        $r->add($rate);
                    }
                }
            }
            $distances->add($r);
        }

        return view('admin.tariffs.distances', [
            'regions' => json_encode($regions),
            // 'polygons' => $_polygons,
            'distances' => $distances,
            'regionsCollection' => $regionsCollection,
            'season_id' => $season_id,
            'seasons' => $seasons,
        ]);
    }

    public function updateDistances(Request $r){
        try {
            $distances = $r->input('distances', []);
            for($i = 0; $i < count($distances); $i++) if(isset($distances[$i])) $this->dispatchSync(new ChangeDistancesJob($r, $i));
            return redirect()->route('admin.regions.distances')->with('success', trans('admin.update_success'));
        } catch (\Exception $e){
            return redirect()->back()->with('danger', trans('admin.update_failed'));
        }
    }

    public function export(){
        $carTypes = CarType::get();
        
        $titles = [
            // 'ID',
            'От',
            'До',
            'Коэффициент объемного веса',
            'Коэфициент за км.',
            'Расстояние км',
            'Сумма'
            // 'ID от',
            // 'ID до',
            // 'Дата',
        ];

        $exportData = collect();
        $tariffData = collect();

        foreach ($carTypes as $carType) {
            $carTypeRates = CarTypeRate::select('id', 'region_from_id', 'region_to_id', 'prices_distance', 'distance_between')
                                        ->where('car_type_id', $carType->id)
                                        ->orderBy('region_from_id')
                                        ->where('season_id', 5)
                                        ->get();

            $tariffData->push([$carType->title]);
            
            if($carTypeRates){
                foreach($carTypeRates as $rate){
                    $regionFrom = Region::select('title')->find($rate->region_from_id);
                    $regionTo = Region::select('title')->find($rate->region_to_id);

                    $coeffs = json_decode($rate->prices_distance, true);

                        $lastRation = 0;
                        $rateRation = 0;
                        
                        for($i = 0; $i < count($coeffs); $i++){
                            $distancePrice = $coeffs[$i];
                            if($rate->distance_between <= $distancePrice['distance']){
                                $rateRation = $distancePrice['sum'];
                                break;
                            }
                            $lastRation = $distancePrice['sum'];
                        }
                        if($rateRation <= 0){
                            $rateRation = $lastRation;
                        }
                        $calculatedPrice = ($rate->distance_between * $carType->max_weight * $rateRation) + $rate->min_price;
                        $calculatedPrice = number_format($calculatedPrice, 0, '', ' ');
                    
                    $tariffData->push([
                        // $rate->id,
                        $regionFrom->title,
                        $regionTo->title,
                        $rate->diveder,
                        $rateRation,
                        $rate->distance_between,
                        $calculatedPrice.' сум',
                    ]);
                }
            }
        }

        $exportData->push($titles);
        $exportData->push($tariffData);

        $export = new TariffsExport($exportData);
        return Excel::download($export, config('app.name').' - Prices '.(date('Y-m-d')).'.xlsx');
    }


    public function exportAll(){
        $carTypes = CarType::withTranslation()->orderBy('id')->get();
        $regions = Region::get();
        $season = Season::first();
        $titles = [
            // 'ID',
            'Откуда',
            'Куда',
            'Тип машины',
            'Грузоподёмность',
            'Габариты',
            'Коэф объем веса',
            'Допуск част. погр',
            'Тариф за подачу полн',
            'Тариф за подачу част',
            'Тариф по коду 1',
            'Тариф по коду 2',
            'Тариф по коду 3',
            'Тариф по коду 4',
            'Коэф част погр',
            'Коэф выше стандартного расст',
            'Расстояние',
            'Коэф туда-обратно'
        ];
        $exportData = collect();
        $exportData->push($titles);
        
        foreach ($regions as $region) {
            foreach ($regions as $r) {
                foreach ($carTypes as $carType){
                    $data = collect();
                    $rate = CarTypeRate::where('season_id', 3)
                                        ->where('car_type_id', $carType->id)
                                        ->where('region_from_id', $region->id)
                                        ->where('region_to_id', $r->id)
                                        ->orderByDesc('region_from_id')
                                        ->first();

                    if($rate){
                        $data->push($region->title);
                        $data->push($r->title);
                        $data->push($carType->title);
                        $data->push($carType->max_weight);
                        $data->push($carType->dimension_x*$carType->dimension_y*$carType->dimension_z);
                        $data->push($rate->divider);
                        $data->push($rate->not_full_min_value);
                        $data->push((int)$rate->min_price);
                        $data->push((int)$rate->not_full_min_price);
                        
                        $pricesDistance = json_decode($rate->prices_distance, true);
                        for($i = 0; $i < 4; $i++){
                            $data->push($pricesDistance[$i]['sum'] ?? 0);
                        }
                        $data->push($rate->not_full_ratio);
                        $data->push($rate->over_limit_coeff);
                        $data->push($rate->distance_between);
                        $data->push($rate->trip_discount);
                    }
                    $exportData->push($data);
                }
            }
        }
        $export = new TariffsExport($exportData);
        return Excel::download($export, config('app.name').' - Tariffs '.(date('Y-m-d')).'.xlsx');
    }
}
