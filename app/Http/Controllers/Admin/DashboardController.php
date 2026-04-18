<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\Dashboard\TrackingFunnelDashboardService;
use App\Services\Admin\Dashboard\TruckBookingsDashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, TruckBookingsDashboardService $dashboardService)
    {
        return view('admin.dashboards.index', $dashboardService->build($request->only(array(
            'year',
            'month',
            'compare',
            'compare_year',
            'compare_month',
            'period',
            'client_group',
        ))));
    }

    public function funnel(Request $request, TrackingFunnelDashboardService $dashboardService)
    {
        return view('admin.dashboards.funnel', $dashboardService->build($request->only(array(
            'range',
            'platform',
            'date_from',
            'date_to',
        ))));
    }
}
