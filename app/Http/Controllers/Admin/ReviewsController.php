<?php

namespace App\Http\Controllers\Admin;

use App\Domain\TruckBookings\Filters\TruckBookingsFilter;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    public function index(Request $request)
    {
        $filter = new TruckBookingsFilter($request);
        $reviews = TruckBooking::where(function ($query) {
            $query->where('rating', '>', 0)->orWhere('driver_rating', '>', 0);
        })
            ->filter($filter)
            ->with('user', 'driver', 'company')
            ->paginateFilter();
        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'filters' => $filter->filters(),
        ]);
    }

    public function delete(TruckBooking $booking, Request $request)
    {
        $type = $request->input('type');
        if($type == 'driver') {
            $booking->driver_review = null;
            $booking->driver_rating = null;
        }
        if ($type == 'user') {
            $booking->review = null;
            $booking->rating = null;
        }
        if ($type == 'review') {
            $booking->review = null;
            $booking->rating = null;
            $booking->driver_review = null;
            $booking->driver_rating = null;
        }
        $booking->save();
        return redirect()->back()->with('success', trans('admin.destroy_success'));
    }
}
