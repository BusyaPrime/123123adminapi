<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Domain\CarTypes\Models\CarTypeRatesHistory;

class CarTypeRatesHistoryController extends Controller
{
    public function index(Request $request){

        $histories = CarTypeRatesHistory::with('car_type_rate')
                                            ->with('car_type')
                                            ->with('region_from')
                                            ->with('region_to')
                                            ->with('season')
                                            ->orderByDesc('created_at')
                                            ->paginate(80);

        return view(
            'admin.car-type-rates-history.index',
            [
                'histories' => $histories
            ]
        );
    }
}
