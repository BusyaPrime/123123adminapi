<?php

namespace App\Http\Controllers\TAdmin;

use App\Domain\Regions\Models\Region;
use App\Domain\TruckBookings\Filters\TruckBookingsFilter;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->input('_company');
        $request->merge(['company_id' => $company->id]);
        $filter = new TruckBookingsFilter($request);
        $reviews = TruckBooking::where(function ($query) {
            $query->where('rating', '>', 0)->orWhere('driver_rating', '>', 0);
        })
            ->filter($filter)
            ->with(['user.user', 'driver.user', 'company', 'cargoType'])
            ->where('company_id', $company->id)
            ->paginateFilter();

        $regionTitles = Region::query()
            ->whereIn('id', $reviews->getCollection()->pluck('region_from_id')->merge($reviews->getCollection()->pluck('region_to_id'))->filter()->unique()->values())
            ->pluck('title', 'id');

        return view('admin2.reviews.index', [
            'reviews' => $reviews,
            'regionTitles' => $regionTitles,
            'filters' => $filter->filters(),
        ]);
    }
}
